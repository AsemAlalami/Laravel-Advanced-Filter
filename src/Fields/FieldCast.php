<?php


namespace AsemAlalami\LaravelAdvancedFilter\Fields;


use Illuminate\Support\Str;

trait FieldCast
{
    protected function getFieldCastType(string $field, $casts)
    {
        if (array_key_exists($field, $casts)) {

            if ($this->isDatetime($casts[$field])) {
                return 'datetime';
            }

            if ($this->isDate($casts[$field])) {
                return 'date';
            }

            if ($this->isNumeric($casts[$field])) {
                return 'decimal';
            }

        }

        return 'string';
    }

    protected function isDatetime(string $cast)
    {
        return Str::startsWith($cast, 'datetime');
    }

    protected function isDate(string $cast)
    {
        return Str::startsWith($cast, 'date');
    }

    protected function isNumeric(string $cast)
    {
        return Str::startsWith($cast, [
            'int',
            'integer',
            'real',
            'float',
            'double',
            'decimal',
            'bool',
            'boolean',
        ]);
    }
}
