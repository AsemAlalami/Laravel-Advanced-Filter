<?php


namespace AsemAlalami\LaravelAdvancedFilter\Exceptions;


use AsemAlalami\LaravelAdvancedFilter\Fields\FieldCast;
use Exception;

class DatatypeNotFound extends Exception
{
    public function __construct(string $datatype)
    {
        $message = "The {$datatype} datatype is not supported, supported datatypes: " .
            implode(', ', FieldCast::$primitiveDatatypes);

        parent::__construct($message, 0, null);
    }
}
