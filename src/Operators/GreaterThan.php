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
            $value = $value->endOfDay();
        }

        if ($field->getDatatype() == 'datetime' && $value->second == 0) {
            $value = $value->endOfMinute();
        }

        return $builder->where($column, '>', $value, $conjunction);
    }
}
