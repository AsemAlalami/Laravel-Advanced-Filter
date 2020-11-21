<?php


namespace AsemAlalami\LaravelAdvancedFilter\Exceptions;


use Exception;

class UnsupportedDriverException extends Exception
{
    public function __construct(string $driver, string $type)
    {
        $message = "The `{$driver}` driver unsupported for `{$type}` fields yet.";

        parent::__construct($message, 0, null);
    }
}
