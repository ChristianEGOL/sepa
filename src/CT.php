<?php

namespace EGOL\Sepa;

class CT extends SEPA
{
    public function __construct($groupName, $iban, $bic)
    {
        parent::__construct($groupName, $iban, $bic, 0);

        $this->startElement('Document');

        $this->startAttribute('xsi:schemaLocation');
        $this->text('urn:iso:std:iso:20022:tech:xsd:pain.001.003.03 pain.001.003.03.xsd');
        $this->endAttribute();

        $this->startAttribute('xmlns');
        $this->text('urn:iso:std:iso:20022:tech:xsd:pain.001.003.03');
        $this->endAttribute();

        $this->startAttribute('xmlns:xsi');
        $this->text('http://www.w3.org/2001/XMLSchema-instance');
        $this->endAttribute();

        $this->startElement('CstmrCdtTrfInitn');
    }
    
    /**
     * Finalisiert das XML Dokument.
     */
    public function create()
    {
        $this->buildGroupHeader();
        $this->buildDebitor();

        for ($i = 0; $i <= $this->DebitorCnt - 1; ++$i) {
            $this->buildCreditor($i);
        }

        $this->save();
    }

    private function buildDebitor()
    {
        $this->startElement('PmtInf');
        $this->writeElement('PmtInfId', $this->generatePaymentId());
        $this->writeElement('PmtMtd', 'TRF');
        $this->writeElement('BtchBookg', 'true');
        $this->writeElement('NbOfTxs', $this->DebitorCnt);
        $this->writeElement('CtrlSum', $this->getTotal());

        $this->startElement('PmtTpInf');

        $this->startElement('SvcLvl');
        $this->writeElement('Cd', 'SEPA');
        $this->endElement();

        $this->endElement();

        $this->writeElement('ReqdExctnDt', date('Y-m-d', time() + 86400));

        $this->startElement('Dbtr');
        $this->writeElement('Nm', $this->grpHeader['Nm']);
        $this->endElement();

        $this->startElement('DbtrAcct');
        $this->startElement('Id');
        $this->writeElement('IBAN', $this->Accountant['IBAN']);
        $this->endElement();
        $this->endElement();

        $this->startElement('DbtrAgt');
        $this->startElement('FinInstnId');
        $this->writeElement('BIC', $this->Accountant['BIC']);
        $this->endElement();
        $this->endElement();

        $this->writeElement('ChrgBr', 'SLEV');
    }

    private function buildCreditor($cnt)
    {
        $this->startElement('CdtTrfTxInf');
        $this->startElement('PmtId');
        $this->writeElement('EndToEndId', $this->generateEndToEndId());
        $this->endElement();
        $this->startElement('Amt');
        $this->startElement('InstdAmt');
        $this->writeAttribute('Ccy', 'EUR');
        $this->text($this->Debitor[$cnt]['InstdAmt']);
        $this->endElement();
        $this->endElement();

        $this->startElement('CdtrAgt');
        $this->startElement('FinInstnId');
        $this->writeElement('BIC', $this->Debitor[$cnt]['BIC']);
        $this->endElement();
        $this->endElement();

        $this->startElement('Cdtr');
        $this->writeElement('Nm', $this->Debitor[$cnt]['Nm']);
        $this->endElement();

        $this->startElement('CdtrAcct');
        $this->startElement('Id');
        $this->writeElement('IBAN', $this->Debitor[$cnt]['IBAN']);
        $this->endElement();
        $this->endElement();

        if (!empty($this->Debitor[$cnt]['verwendungszweck'])) {
            $this->startElement('RmtInf');
            $this->writeElement('Ustrd', $this->Debitor[$cnt]['verwendungszweck']);
            $this->endElement();
        }
        $this->endElement();
    }
}
