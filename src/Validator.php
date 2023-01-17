<?php

namespace EGOL\SepaXml;

use DOMDocument;
use EGOL\SepaXml\Interface\SepaInterface;
use Exception;

class Validator
{
    const PAIN00800102 = 1;
    const PAIN00800302 = 2;
    const PAIN00100103 = 3;

    protected $errors = [];

    public function __construct($sepa, int $schema)
    {
        $xml = new DOMDocument();
        $xml->loadXML($sepa, LIBXML_NOBLANKS);
        libxml_use_internal_errors(true);

        if (!$xml->schemaValidateSource($this->getSchema($schema))) {
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

    private function getSchema(int $schema)
    {
        switch ($schema) {
            case 1:
                return file_get_contents(__DIR__ . '/Schemas/008.001.02.xsd');
                break;
            case 1:
                return file_get_contents(__DIR__ . '/Schemas/008.003.02.xsd');
                break;
            case 3:
                return file_get_contents(__DIR__ . '/Schemas/001.001.03.xsd');
                break;
            default:
                throw new Exception('Schema not found');
        }
    }
}
