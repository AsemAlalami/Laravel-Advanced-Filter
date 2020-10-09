<?php


namespace AsemAlalami\LaravelAdvancedFilter\Operators;


use AsemAlalami\LaravelAdvancedFilter\Exceptions\UnsupportedOperatorException;
use AsemAlalami\LaravelAdvancedFilter\Fields\Field;
use AsemAlalami\LaravelAdvancedFilter\QueryFormats\QueryFormat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class In extends Operator
{
    /**
     * @inheritDoc
     */
    public function apply(Builder $builder, Field $field, $value, string $conjunction = 'and'): Builder
    {
        if (empty($value)) {
            return $builder;
        }

        if ($field->getDatatype() == 'date') {
            return $this->applyOnDate($builder, $field, $value, $conjunction);
        }

        // to use in NotIn operator
        $notIn = $this->getSqlOperator() != 'IN';

        return $builder->whereIn($field->getColumn(), $this->getSqlValue($value), $conjunction, $notIn);
    }

    /**
     * @inheritDoc
     */
    public function getSqlOperator(): string
    {
        return 'IN';
    }

    protected function getSqlValue($value)
    {
        return Arr::wrap($value);
    }

    public function applyOnCustom(Builder $builder, Field $field, $value, string $conjunction = 'and'): Builder
    {
        if (empty($value)) {
            return $builder;
        }

        $value = implode(",", $this->getSqlValue($value));

        return parent::applyOnCustom($builder, $field, "({$value})", $conjunction);
    }

    public function applyOnCount(Builder $builder, Field $field, $value, string $conjunction = 'and'): Builder
    {
        throw new UnsupportedOperatorException($this->name, 'count');
    }

    private function applyOnDate(Builder $builder, Field $field, $value, string $conjunction = 'and')
    {
        // TODO: cast values to Carbon and format it to date
        $values = $this->getSqlValue($value);

        $bind = implode(',', array_fill(0, count($values), '?'));

        return $builder->whereRaw("DATE(`{$field->getColumn()}`) in ({$bind})", $values, $conjunction);
    }
}
