<?php


namespace AsemAlalami\LaravelAdvancedFilter\Operators;


use AsemAlalami\LaravelAdvancedFilter\Exceptions\UnsupportedOperatorException;
use AsemAlalami\LaravelAdvancedFilter\Fields\Field;
use Illuminate\Database\Eloquent\Builder;
use InvalidArgumentException;

class Between extends Operator
{

    /**
     * @inheritDoc
     */
    public function apply(Builder $builder, Field $field, $value, string $conjunction = 'and'): Builder
    {
        $value = $this->getSqlValue($value);

        if (empty($value)) {
            return $builder;
        }

        $values = ['from' => $field->getCastedValue($value['from']), 'to' => $field->getCastedValue($value['to'])];

        if ($field->getDatatype() == 'date') {
            $castInDB = config('advanced_filter.cast_db_date', false);
            if ($castInDB) {
                return $builder->where(function (Builder $builder) use ($values, $field, $value) {
                    $builder->whereDate($field->getColumn(), '>=', $values['from'])
                        ->whereDate($field->getColumn(), '<=', $values['to']);
                }, null, null, $conjunction);
            }

            $values['to'] = $values['to']->endOfDay(); // when using the between operator
        }

        return $builder->whereBetween($field->getColumn(), array_values($values), $conjunction);
    }

    /**
     * @inheritDoc
     */
    public function getSqlOperator(): string
    {
        return 'BETWEEN';
    }

    protected function getSqlValue($value)
    {
        if (!is_array($value) || !array_key_exists('from', $value) || !array_key_exists('to', $value)) {
            throw new InvalidArgumentException('The between value must be array that contains "from" and "to" keys');
        }

        return $value;
    }

    public function applyOnCustom(Builder $builder, Field $field, $value, string $conjunction = 'and'): Builder
    {
        if (empty($value)) {
            return $builder;
        }

        $sql = "{$field->getColumn()} {$this->getSqlOperator()} ? and ?";

        return $builder->whereRaw($sql, [array_values($this->getSqlValue($value))], $conjunction);
    }

    public function applyOnCount(Builder $builder, Field $field, $value, string $conjunction = 'and'): Builder
    {
        throw new UnsupportedOperatorException($this->name, 'count');
    }
}
