<?php


namespace AsemAlalami\LaravelAdvancedFilter\Operators;


use AsemAlalami\LaravelAdvancedFilter\Fields\Field;
use Illuminate\Database\Eloquent\Builder;

class NotEquals extends Operator
{
    public function apply(Builder $builder, Field $field, $value, string $conjunction = 'and'): Builder
    {
        if (is_null($value)) {
            return $builder->whereNotNull($field->getColumn(), $conjunction);
        }

        if ($field->getDatatype() == 'date') {
            $castInDB = config('advanced_filter.cast_db_date', false);

            if ($castInDB) {
                return $builder->whereDate($field->getColumn(), $this->getSqlOperator(), $value, $conjunction);
            } else {
                return $builder->whereNotBetween($field->getColumn(), [$value, (clone $value)->endOfDay()], $conjunction);
            }
        }

        if ($field->getDatatype() == 'datetime') {
            return $builder->whereNotBetween($field->getColumn(), [$value, (clone $value)->endOfMinute()], $conjunction);
        }

        return $builder->where($field->getColumn(), $this->getSqlOperator(), $value, $conjunction);
    }

    public function getSqlOperator(): string
    {
        return '!=';
    }
}
