<?php


namespace AsemAlalami\LaravelAdvancedFilter\Operators;


use AsemAlalami\LaravelAdvancedFilter\Exceptions\UnsupportedOperatorException;
use AsemAlalami\LaravelAdvancedFilter\Fields\Field;
use Illuminate\Database\Eloquent\Builder;

class Contains extends Operator
{
    /**
     * @inheritDoc
     */
    public function apply(Builder $builder, Field $field, $value, string $conjunction = 'and'): Builder
    {
        return $builder->where($field->getColumn(), $this->getSqlOperator(), $this->getSqlValue($value), $conjunction);
    }

    /**
     * @inheritDoc
     */
    public function getSqlOperator(): string
    {
        return 'LIKE';
    }

    protected function getSqlValue($value)
    {
        return "%{$value}%";
    }

    public function applyOnCount(Builder $builder, Field $field, $value, string $conjunction = 'and'): Builder
    {
        throw new UnsupportedOperatorException($this->name, 'count');
    }
}
