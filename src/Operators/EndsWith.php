<?php


namespace AsemAlalami\LaravelAdvancedFilter\Operators;


class EndsWith extends Contains
{
    protected function getSqlValue($value)
    {
        return "%{$value}";
    }
}
