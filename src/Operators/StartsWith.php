<?php


namespace AsemAlalami\LaravelAdvancedFilter\Operators;


class StartsWith extends Contains
{
    protected function getSqlValue($value)
    {
        return "{$value}%";
    }
}
