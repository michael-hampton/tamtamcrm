<?php


namespace App\Components\Import;


use Exception;

/**
 * Class CsvImporterException
 * @package App\CsvImporter\Exceptions
 */
class ImportValidationException extends Exception
{
    /**
     * ImportValidationException constructor.
     * @param $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct($message, $code = 401, Exception $previous = null)
    {
        parent::__construct(json_encode($message), $code, $previous);
    }
}