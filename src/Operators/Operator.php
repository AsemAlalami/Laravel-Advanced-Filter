<?php


namespace AsemAlalami\LaravelAdvancedFilter\Operators;


use Illuminate\Database\Eloquent\Builder;

abstract class Operator
{
    /** @var string $name */
    public $name;
    /** @var array $aliases Remove it */
    protected $aliases = [];

    public abstract function apply(Builder $builder, string $field, $value, string $conjunction = 'and'): Builder;

    public static function getFunction($operatorName)
    {
        $prefixOperatorFunction = config('advanced_filter.prefix_operator_function', 'filterWhere');

        return "{$prefixOperatorFunction}{$operatorName}";
    }

    public function setAliases(array $aliases)
    {
        $this->aliases = $aliases;
    }

    public function __get($name)
    {
        if ($name == 'name' && empty($this->name)) {
            return get_class($this);
        }

        if ($name == 'aliases' && empty($this->aliases)) {
            return [mb_strtolower(get_class($this))];
        }

        return $this->{$name};
    }
}
