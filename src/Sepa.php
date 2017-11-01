<?php

namespace EGOL\Sepa;

use \XMLWriter;

abstract class Sepa extends XMLWriter
{
    protected $grpHeader;
    protected $pmtInf;
    protected $DebitorCnt;
    protected $Institution;
    protected $Accountant;
    protected $Debitor;
    protected $Creditor;
    protected $savepath;

    /**
     * Dokumentinfos für die Lastschrift werden erstellt.
     */
    public function __construct($groupName, $iban, $bic, $glaeubigerId)
    {
        $this->Accountant['BIC'] = $bic;
        $this->Accountant['IBAN'] = $iban;
        $this->grpHeader['Nm'] = $groupName;
        $this->Accountant['GlbgrId'] = $glaeubigerId;
        $this->grpHeader['MsgId'] = $this->generateMsgId();

        $this->DebitorCnt = 0;
        $this->savepath = 'sepa.xml';
        $this->output = 0;

        $this->openMemory();
        $this->setIndent(true);
        $this->setIndentString(' ');
        $this->startDocument('1.0', 'UTF-8');
        $this->endDocument();
    }

    /**
     * Schließt das XML Dokument.
     */
    public function __destruct()
    {
        // PmtInf
        $this->endElement();
        // CstmrDrctDbtInitn
        $this->endElement();
        // Document
        $this->endElement();

        $fp = fopen($this->savepath, 'w');
        fwrite($fp, $this->outputMemory());
        fclose($fp);

        header('Content-type: text/xml');
        header('Content-Disposition: attachment; filename="'.'"');
        readfile($this->savepath);
    }

    /**
     * Einen Datensatz hinzufügen
     *
     * @param string $name       Name des Käufers
     * @param string $iban
     * @param string $bic
     * @param float  $amount Einzelsumme
     * @param int    $mandatId Mandat ID
     * @param int    $comment     Verwendungszweck
     */
    public function add($name, $iban, $bic, $amount, $mandatId, $comment)
    {
        $this->Debitor[$this->DebitorCnt]['Nm'] = $Nm;
        $this->Debitor[$this->DebitorCnt]['InstdAmt'] = $amount;
        $this->Debitor[$this->DebitorCnt]['MndtId'] = $mandatId;
        $this->Debitor[$this->DebitorCnt]['IBAN'] = $iban;
        $this->Debitor[$this->DebitorCnt]['BIC'] = $bic;
        $this->Debitor[$this->DebitorCnt]['verwendungszweck'] = $comment;
        ++$this->DebitorCnt;
    }

    /**
     * Erstellt den Group Header.
     */
    protected function buildGroupHeader()
    {
        $this->startElement('GrpHdr');
        $this->writeElement('MsgId', $this->grpHeader['MsgId']);
        $this->writeElement('CreDtTm', date('Y-m-d\TH:i:s'));
        $this->writeElement('NbOfTxs', $this->DebitorCnt);

        $this->startElement('InitgPty');
        $this->writeElement('Nm', $this->grpHeader['Nm']);
        $this->endElement();

        // GrpHdr
        $this->endElement();
    }

    protected function getTotal()
    {
        $sum = 0;
        for ($i = 0; $i <= $this->DebitorCnt - 1; ++$i) {
            $sum += $this->Debitor[$i]['InstdAmt'];
        }

        return $sum;
    }

    public function setSavePath($path)
    {
        $this->savepath = $path;
    }

    /**
     * Generate Msg ID
     */
    protected function generateMsgId()
    {
        return $this->Accountant['BIC'].rand(100000000, 999999999).date('dmYHis');
    }

    /**
     * Generate Payment ID
     */
    protected function generatePaymentId()
    {
        return 'PALBOO'.rand(100000000, 999999999).date('His');
    }

    /**
     * Generate End-To-End ID
     */
    protected function generateEndToEndId()
    {
        return 'PALBOOCSTMR'.rand(100000000, 999999999).date('His');
    }
}
