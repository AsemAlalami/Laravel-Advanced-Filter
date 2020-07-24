<?php


namespace AsemAlalami\LaravelAdvancedFilter\Operators;


use AsemAlalami\LaravelAdvancedFilter\Fields\Field;
use Illuminate\Database\Eloquent\Builder;

class NotEqual extends Operator
{
    public $name = 'NotEqual';

    public function apply(Builder $builder, Field $field, $value, string $conjunction = 'and'): Builder
    {
        $column = $field->getColumn();

        if ($field->getDatatype() == 'date') {
            $castInDB = config('advanced_filter.cast_db_date', false);

            if ($castInDB) {
                return $builder->whereDate($column, '!=', $value, $conjunction);
            } else {
                return $builder->where(function (Builder $builder) use ($value, $column) {
                    $builder->where($column, '<', $value)
                        ->orWhere($column, '>', $value->clone()->endOfDay());
                }, null, null, $conjunction);
            }
        }

        if ($field->getDatatype() == 'datetime' && $value->second == 0) {
            return $builder->where(function (Builder $builder) use ($value, $column) {
                $builder->where($column, '<', $value)
                    ->orWhere($column, '>', $value->clone()->endOfMinute());
            }, null, null, $conjunction);
        }

        return $builder->where($column, '!=', $value, $conjunction);
    }
}
