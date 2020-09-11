<?php


namespace AsemAlalami\LaravelAdvancedFilter\Exceptions;


use Exception;

class OperatorNotFoundException extends Exception
{
    public function __construct(string $operator)
    {
        $message = "The {$operator} operator not found, please check the operators in the config file";

        parent::__construct($message, 0, null);
    }
}
