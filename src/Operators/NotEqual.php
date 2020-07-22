<?php


namespace AsemAlalami\LaravelAdvancedFilter\Operators;


use Illuminate\Database\Eloquent\Builder;

class NotEqual extends Operator
{
    public $name = 'NotEqual';
    public $aliases = ['notEqual', '!='];

    public function apply(Builder $builder, string $field, $value, string $conjunction = 'and'): Builder
    {
        return $builder->where($field, '!=', $value, $conjunction);
    }
}
