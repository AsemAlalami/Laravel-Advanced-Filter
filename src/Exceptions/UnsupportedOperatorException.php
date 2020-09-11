<?php


namespace AsemAlalami\LaravelAdvancedFilter\Exceptions;


use Exception;

class UnsupportedOperatorException extends Exception
{
    public function __construct(string $operator, string $type)
    {
        $message = "The `{$operator}` operator unsupported for `{$type}` fields";

        parent::__construct($message, 0, null);
    }
}
