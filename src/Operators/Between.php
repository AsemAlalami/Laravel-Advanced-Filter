<?php


namespace AsemAlalami\LaravelAdvancedFilter\Operators;


use AsemAlalami\LaravelAdvancedFilter\Fields\Field;
use AsemAlalami\LaravelAdvancedFilter\QueryFormats\QueryFormat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
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

        if ($field->getDatatype() == 'date') {
            $castInDB = config('advanced_filter.cast_db_date', false);
            $values = [$field->getCastedValue($value['from']), $field->getCastedValue($value['to'])->endOfDay()];

            if ($castInDB) {
                return $builder->where(function (Builder $builder) use ($values, $field, $value) {
                    $builder->whereDate($field->getColumn(), '>=', $values[0])
                        ->whereDate($field->getColumn(), '<=', $values[1]);
                }, null, null, $conjunction);
            } else {
                return $builder->whereBetween($field->getColumn(), $values, $conjunction);
            }
        }

        if ($field->getDatatype() == 'datetime' && $value->second == 0) {
            $values = [
                $field->getCastedValue($value['from'])->startOfMinute(),
                $field->getCastedValue($value['to'])->endOfMinute()
            ];

            return $builder->whereBetween($field->getColumn(), $values, $conjunction);
        }

        return $builder->whereBetween($field->getColumn(), array_values($value), $conjunction);
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
}
