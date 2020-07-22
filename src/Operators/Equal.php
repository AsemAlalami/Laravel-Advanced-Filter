<?php


namespace AsemAlalami\LaravelAdvancedFilter\Operators;


use Illuminate\Database\Eloquent\Builder;

class Equal extends Operator
{
    public $name = 'Equal';
    public $aliases = ['equal', '='];

    public function apply(Builder $builder, string $field, $value, string $conjunction = 'and'): Builder
    {
        return $builder->where($field, '=', $value, $conjunction);
    }
}
