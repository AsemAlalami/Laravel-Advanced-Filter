<?php


namespace AsemAlalami\LaravelAdvancedFilter\Operators;


use AsemAlalami\LaravelAdvancedFilter\Exceptions\UnsupportedOperatorException;
use AsemAlalami\LaravelAdvancedFilter\Fields\Field;
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

        if ($field->getDatatype() == 'date' || $field->getDatatype() == 'datetime') {
            return $this->applyOnDate($builder, $field, $value, $conjunction, $field->getDatatype() == 'datetime');
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

        $sql = "{$field->getColumn()} {$this->getSqlOperator()} ({$value})";

        return $builder->whereRaw($sql, [], $conjunction);
    }

    public function applyOnCount(Builder $builder, Field $field, $value, string $conjunction = 'and'): Builder
    {
        throw new UnsupportedOperatorException($this->name, 'count');
    }

    private function applyOnDate(Builder $builder, Field $field, $value, string $conjunction = 'and', bool $datetime = false)
    {
        // cast to date string
        $values = array_map(function ($v) use ($datetime, $field) {
            $v = $field->getCastedValue($v);

            return $datetime ? $v->toDateTimeString() : $v->toDateString();
        }, $this->getSqlValue($value));

        $dbFormat = '%Y-%m-%d' . ($datetime ? ' %H:%M:%S' : '');
        $bind = implode(',', array_fill(0, count($values), '?'));

        return $builder->whereRaw("strftime('{$dbFormat}', `{$field->getColumn()}`) in ({$bind})", $values, $conjunction);
    }
}
