<?php


namespace AsemAlalami\LaravelAdvancedFilter\Operators;


use AsemAlalami\LaravelAdvancedFilter\Exceptions\UnsupportedOperatorException;
use AsemAlalami\LaravelAdvancedFilter\Fields\Field;
use Illuminate\Database\Eloquent\Builder;

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
