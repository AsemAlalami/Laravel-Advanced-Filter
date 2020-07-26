<?php


namespace AsemAlalami\LaravelAdvancedFilter\Operators;


use AsemAlalami\LaravelAdvancedFilter\Fields\Field;
use Illuminate\Database\Eloquent\Builder;

class GreaterThan extends Operator
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
                return $builder->whereDate($column, '>', $value, $conjunction);
            }

            $value = $value->endOfDay();
        }

        if ($field->getDatatype() == 'datetime' && $value->second == 0) {
            $value = $value->endOfMinute();
        }

        return $builder->where($column, '>', $value, $conjunction);
    }

    public function applyOnCount(Builder $builder, Field $field, $value, string $conjunction = 'and'): Builder
    {
        return $builder->whereHas($field->getRelation(), $field->countCallback, '>', $value);
    }
}
