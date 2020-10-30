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

    // TODO: I think the best way to filter relation fields by using "whereDoesntHave" and "IN" operator
}
