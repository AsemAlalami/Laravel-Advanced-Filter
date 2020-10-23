<?php


namespace AsemAlalami\LaravelAdvancedFilter\Operators;


use AsemAlalami\LaravelAdvancedFilter\Fields\Field;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class Equals extends Operator
{
    /**
     * @inheritDoc
     * @param Carbon $value
     */
    public function apply(Builder $builder, Field $field, $value, string $conjunction = 'and'): Builder
    {
        if (is_null($value)) {
            return $builder->whereNull($field->getColumn(), $conjunction);
        }

        if ($field->getDatatype() == 'date') {
            $castInDB = config('advanced_filter.cast_db_date', false);

            if ($castInDB) {
                return $builder->whereDate($field->getColumn(), $this->getSqlOperator(), $value, $conjunction);
            } else {
                // "between" will not keep indexing on the column, just as an option :)
                return $builder->whereBetween($field->getColumn(), [$value, $value->clone()->endOfDay()], $conjunction);
            }
        }

        if ($field->getDatatype() == 'datetime' && $value->second == 0) {
            $value = [$value->startOfMinute(), $value->clone()->endOfMinute()];

            return $builder->whereBetween($field->getColumn(), $value, $conjunction);
        }

        return $builder->where($field->getColumn(), $this->getSqlOperator(), $value, $conjunction);
    }

    public function getSqlOperator(): string
    {
        return '=';
    }
}
