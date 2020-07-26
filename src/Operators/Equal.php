<?php


namespace AsemAlalami\LaravelAdvancedFilter\Operators;


use AsemAlalami\LaravelAdvancedFilter\Fields\Field;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class Equal extends Operator
{
    public $name = 'Equal';

    public function apply(Builder $builder, Field $field, $value, string $conjunction = 'and'): Builder
    {
        $column = $field->getColumn();

        if ($field->getDatatype() == 'date') {
            $castInDB = config('advanced_filter.cast_db_date', false);

            if ($castInDB) {
                return $builder->whereDate($column, '=', $value, $conjunction);
            } else {
                return $builder->where(function (Builder $builder) use ($value, $column) {
                    $builder->where($column, '>=', $value)
                        ->where($column, '<=', $value->clone()->endOfDay());
                }, null, null, $conjunction);
            }
        }

        /** @var Carbon $value */
        if ($field->getDatatype() == 'datetime' && $value->second == 0) {
            return $builder->where(function (Builder $builder) use ($value, $column) {
                $builder->where($column, '>=', $value->startOfMinute())
                    ->where($column, '<=', $value->clone()->endOfMinute());
            }, null, null, $conjunction);
        }

        return $builder->where($column, '=', $value, $conjunction);
    }

    public function applyOnCount(Builder $builder, Field $field, $value, string $conjunction = 'and'): Builder
    {
        return $builder->whereHas($field->getRelation(), $field->countCallback, '=', $value);
    }
}
