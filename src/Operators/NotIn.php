<?php


namespace AsemAlalami\LaravelAdvancedFilter\Operators;


class NotIn extends In
{
    /**
     * @inheritDoc
     */
    public function getSqlOperator(): string
    {
        return 'NOT IN';
    }
}
