<?php


namespace AsemAlalami\LaravelAdvancedFilter\Operators;


class NotStartsWith extends StartsWith
{
    public function getSqlOperator(): string
    {
        return "NOT LIKE";
    }
}
