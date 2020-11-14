<?php


namespace AsemAlalami\LaravelAdvancedFilter\Operators;


class NotContains extends Contains
{
    /**
     * @inheritDoc
     */
    public function getSqlOperator(): string
    {
        return 'NOT LIKE';
    }
}
