<?php


namespace AsemAlalami\LaravelAdvancedFilter\Operators;


use AsemAlalami\LaravelAdvancedFilter\Fields\Field;
use Illuminate\Database\Eloquent\Builder;

class LessThan extends Operator
{
    /**
     * @inheritDoc
     */
    public function apply(Builder $builder, Field $field, $value, string $conjunction = 'and'): Builder
    {
        $column = $field->getColumn();

        if ($field->getDatatype() == 'date') {
            $castInDB = config('advanced_filter.cast_db_date', false);

            if ($castInDB) {
                return $builder->whereDate($column, '<', $value, $conjunction);
            }

            $value = $value->startOfDay();
        }

        if ($field->getDatatype() == 'datetime' && $value->second == 0) {
            $value = $value->startOfMinute();
        }

        return $builder->where($column, '<', $value, $conjunction);
    }

    public function getSqlOperator(): string
    {
        return '<';
    }
}
