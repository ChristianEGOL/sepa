<?php

namespace EGOL\SepaXml;

use EGOL\SepaXml\Contracts\SepaContract;
use \XMLWriter;

abstract class Sepa extends XMLWriter implements SepaContract
{
    protected $grpHeader;
    protected $pmtInf;
    protected $DebitorCnt;
    protected $Institution;
    protected $Accountant;
    protected $Debitor;
    protected $Creditor;
    protected $savepath;
    protected $company;
    protected $output;

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

    public function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     * Schließt das XML Dokument.
     */
    public function save()
    {
        $this->closeDocument();

        $fp = fopen($this->savepath, 'w');
        fwrite($fp, $this);
        fclose($fp);
    }

    /**
     * Informationen für den Oberen Teil im Knoten <PmtInf>.
     *
     * @param string $SeqTp        Sequenz TYP (FRST, RCUR, OOFF, FNAL)
     */
    public function setPaymentInformations($SeqTp = 'OOFF')
    {
        $this->pmtInf['SeqTp'] = $SeqTp;
    }

    public function download()
    {
        header('Content-type: text/xml');
        header('Content-Disposition: attachment; filename="' . $this->savepath . '"');
        echo $this;
    }

    public function output()
    {
        header('Content-type: text/xml');

        echo $this;
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
        $this->Debitor[$this->DebitorCnt]['Nm'] = $name;
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
        $this->writeElement('CtrlSum', $this->getTotal());

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
        return $this->Accountant['BIC'] . rand(100000000, 999999999) . date('dmYHis');
    }

    /**
     * Generate Payment ID
     */
    protected function generatePaymentId()
    {
        return $this->company . rand(100000000, 999999999) . date('His');
    }

    /**
     * Generate End-To-End ID
     */
    protected function generateEndToEndId()
    {
        return $this->company . 'CSTMR' . rand(100000000, 999999999) . date('His');
    }

    public function __toString()
    {
        $this->closeDocument();

        return $this->outputMemory();
    }


    private function closeDocument()
    {
        // PmtInf
        $this->endElement();
        // CstmrDrctDbtInitn
        $this->endElement();
        // Document
        $this->endElement();
    }
}
