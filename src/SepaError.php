<?php

namespace EGOL\Sepa;

abstract class SepaError
{
    private static $error = array(
        1 => 'ERROR_UNKNOWN (unbekannte Prüfziffermethode – sollte eigentlich nie auftreten, außer evtl. bei Kreditkartennummern, bei denen der Kreditkartentyp nicht identifiziert werden kann)',
        2 => 'ERROR_NOT_IMPLEMENTED (nicht implementierte Prüfziffermethode – sollte eigentlich auch nie auftreten)',
        3 => 'ERROR_NO_PRUEFZIFFER (diese Bank verwendet generell keine Prüfziffern)',
        4 => 'ERROR_SPECIAL (dies ist eine Kontonummer aus einem speziellen Kontonummernbereich dieser Bank, innerhalb desen keine Prüfung möglich ist)',
        5 => 'ERROR_BLZ_ONLY (es wurde nur die Bankleitzahl, nicht jedoch die Kontonummer geprüft; z.B. weil für dieses Land keine Kontonummernprüfung möglich ist)',
        6 => 'ERROR_IBAN_COUNTRY_NOT_BOOKED (die IBAN-Prüfziffer ist in Ordnung; eine weitergehende Prüfung wurde nicht durchgeführt, weil Sie das betreffende Land nicht gebucht haben)',
        7 => 'ERROR_IBAN_COUNTRY_NOT_AVAILABLE (die IBAN-Prüfziffer ist in Ordnung; eine weitergehende Prüfung wurde nicht durchgeführt, weil das betreffende Land nicht geprüft werden kann)',
        12 => 'ERROR_KTO_NO_DIGITS (Kontonummer enthält nicht nur Ziffern)',
        13 => 'ERROR_KTO_PRUEFZIFFER (Kontonummer falsch, Prüfziffer paßt nicht)',
        14 => 'ERROR_KTO_ZERO (Kontonummer ist 0 und damit unzulässig)',
        15 => 'ERROR_KTO_TYPE (Kontoartenziffer ist ungültig)',
        16 => 'ERROR_KTO_LEN8 (Kontonummer muß genau acht Stellen lang sein)',
        17 => 'ERROR_KTO_LEN9 (Kontonummer muß genau neun Stellen lang sein)',
        18 => 'ERROR_KTO_LEN7 (Kontonummer muß genau sieben Stellen lang sein)',
        19 => 'ERROR_KTO_LEN8OR10 (Kontonummer muß acht oder zehn Stellen lang sein)',
        20 => 'ERROR_KTO_LEN10 (Kontonummer muß genau zehn Stellen lang sein)',
        21 => 'ERROR_KTO_LEN7OR8 (Kontonummer muß sieben oder acht Stellen lang sein)',
        22 => 'ERROR_KTO_IBAN_PRUEFZIFFER (IBAN-Prüfziffer falsch; weitergehende Prüfungen wurden daher nicht durchgeführt)',
        23 => 'ERROR_KTO_BLACKLIST (diese Bankverbindung steht auf Ihrer Blacklist)',
        24 => 'ERROR_GET_IBAN (die IBAN kann nicht ermittelt werden; kann nur bei der Funktion KtoPruef.GetIban von KONTOPRUEF-OFFLINE auftreten; für nähere Angaben siehe dort)',
        25 => 'ERROR_KTO_IBAN_COUNTRY (die angegebene IBAN konnte keinem Land zugeordnet werden)',
        26 => 'ERROR_KTO_IBAN_FORMAT (das Format der angegebenen IBAN passt nicht zum Land)',
    );

    public static function get($error)
    {
        return self::$error[$error];
    }
}
