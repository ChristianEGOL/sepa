<?php

namespace EGOL\SepaXml;

class DD extends SEPA
{
    public function __construct($groupName, $iban, $bic, $glaeubigerId, $paymentInformation = 'OOFF')
    {
        parent::__construct($groupName, $iban, $bic, $glaeubigerId);

        $this->setPaymentInformations($paymentInformation);

        $this->startElement('Document');

        $this->startAttribute('xsi:schemaLocation');
        $this->text('urn:iso:std:iso:20022:tech:xsd:pain.008.003.02 pain.008.003.02.xsd');
        $this->endAttribute();

        $this->startAttribute('xmlns');
        $this->text('urn:iso:std:iso:20022:tech:xsd:pain.008.003.02');
        $this->endAttribute();

        $this->startAttribute('xmlns:xsi');
        $this->text('http://www.w3.org/2001/XMLSchema-instance');
        $this->endAttribute();

        $this->startElement('CstmrDrctDbtInitn');
    }

    /**
     * Finalisiert das XML Dokument.
     */
    public function create()
    {
        $this->buildGroupHeader();
        $this->buildPaymentInformations();
        $this->buildCreditor();

        for ($i = 0; $i <= $this->DebitorCnt - 1; ++$i) {
            $this->buildDebitor($i);
        }

        $this->save();
    }

    /**
     * Erstellt den Creditor Part.
     */
    private function buildCreditor()
    {
        $this->startElement('Cdtr');
        $this->writeElement('Nm', $this->grpHeader['Nm']);
        $this->startElement('PstlAdr');
        $this->writeElement('Ctry', 'DE');
        $this->endElement();
        $this->endElement();

        $this->startElement('CdtrAcct');
        $this->startElement('Id');
        $this->writeElement('IBAN', $this->Accountant['IBAN']);
        $this->endElement();
        $this->endElement();

        $this->startElement('CdtrAgt');
        $this->startElement('FinInstnId');
        $this->writeElement('BIC', $this->Accountant['BIC']);
        $this->endElement();
        $this->endElement();
        $this->writeElement('ChrgBr', 'SLEV');

        $this->startElement('CdtrSchmeId');
        $this->startElement('Id');
        $this->startElement('PrvtId');
        $this->startElement('Othr');
        $this->writeElement('Id', $this->Accountant['GlbgrId']);
        $this->startElement('SchmeNm');
        $this->writeElement('Prtry', 'SEPA');
        $this->endElement();
        $this->endElement();
        $this->endElement();
        $this->endElement();
        $this->endElement();
    }

    private function buildPaymentInformations()
    {
        $this->startElement('PmtInf');
        $this->writeElement('PmtInfId', $this->generatePaymentId());
        $this->writeElement('PmtMtd', 'DD');
            // $this->writeElement('BtchBookg', 'false');
            $this->writeElement('NbOfTxs', $this->DebitorCnt);
        $this->writeElement('CtrlSum', $this->getTotal());

        $this->startElement('PmtTpInf');

        $this->startElement('SvcLvl');
        $this->writeElement('Cd', 'SEPA');
        $this->endElement();

        $this->startElement('LclInstrm');
        $this->writeElement('Cd', 'CORE');
        $this->endElement();

        $this->writeElement('SeqTp', $this->pmtInf['SeqTp']);

        $this->endElement();

        $this->writeElement('ReqdColltnDt', date('Y-m-d', time() + 86400));
    }

    private function buildDebitor($cnt)
    {
        $this->startElement('DrctDbtTxInf');
        $this->startElement('PmtId');
        $this->writeElement('EndToEndId', $this->generateEndToEndId());
        $this->endElement();
        $this->startElement('InstdAmt');
        $this->writeAttribute('Ccy', 'EUR');
        $this->text($this->Debitor[$cnt]['InstdAmt']);
        $this->endElement();

        $this->startElement('DrctDbtTx');
        $this->startElement('MndtRltdInf');
        $this->writeElement('MndtId', $this->Debitor[$cnt]['MndtId']);
        $this->writeElement('DtOfSgntr', date('Y-m-d'));
        $this->endElement();

        $this->endElement();
        $this->startElement('DbtrAgt');
        $this->startElement('FinInstnId');
        $this->writeElement('BIC', $this->Debitor[$cnt]['BIC']);
        $this->endElement();
        $this->endElement();
        $this->startElement('Dbtr');
        $this->writeElement('Nm', $this->Debitor[$cnt]['Nm']);
        $this->endElement();
        $this->startElement('DbtrAcct');
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
