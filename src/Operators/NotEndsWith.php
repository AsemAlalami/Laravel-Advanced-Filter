<?php


namespace AsemAlalami\LaravelAdvancedFilter\Operators;


class NotEndsWith extends EndsWith
{
    public function getSqlOperator(): string
    {
        return "NOT LIKE";
    }
}
