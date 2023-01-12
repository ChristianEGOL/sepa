<?php

namespace EGOL\SepaXml;

use DOMDocument;

class Validator
{
    protected array $errors = [];

    public function __construct(string $content, string $xsdPath)
    {
        $xml = new DOMDocument();
        $xml->loadXML($content, LIBXML_NOBLANKS);
        libxml_use_internal_errors(true);

        if (!$xml->schemaValidateSource(file_get_contents($xsdPath))) {
            $errors = libxml_get_errors();

            foreach ($errors as $error) {
                $this->errors[] = $this->handle($error);
            }
            libxml_clear_errors();

            return $this->errors;
        }
    }

    public function getErrors()
    {
        return $this->errors;
    }

    private function handle($error)
    {
        $string = "<br/>\n";
        switch ($error->level) {
            case LIBXML_ERR_WARNING:
                $string .= "<b>Warning $error->code</b>: ";
                break;
            case LIBXML_ERR_ERROR:
                $string .= "<b>Error $error->code</b>: ";
                break;
            case LIBXML_ERR_FATAL:
                $string .= "<b>Fatal Error $error->code</b>: ";
                break;
        }
        $string .= trim($error->message);
        if ($error->file) {
            $string .= " in <b>$error->file</b>";
        }
        $string .= " on line <b>$error->line</b>\n";

        return $string;
    }
}
