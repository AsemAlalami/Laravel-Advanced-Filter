<?php


namespace AsemAlalami\LaravelAdvancedFilter\Operators;


use AsemAlalami\LaravelAdvancedFilter\Fields\Field;
use Illuminate\Database\Eloquent\Builder;

abstract class Operator
{
    /** @var string $name */
    protected $name;
    /** @var array $aliases */
    protected $aliases = [];

    /**
     * The function calls when trying to apply the operator on a field
     *
     * @param Builder $builder
     * @param Field $field
     * @param $value
     * @param string $conjunction
     *
     * @return Builder
     */
    public abstract function apply(Builder $builder, Field $field, $value, string $conjunction = 'and'): Builder;

    /**
     * The function calls when trying to apply the operator on a count field
     *
     * @param Builder $builder
     * @param Field $field
     * @param $value
     * @param string $conjunction
     *
     * @return Builder
     */
    public function applyOnCount(Builder $builder, Field $field, $value, string $conjunction = 'and'): Builder
    {
        return $builder;
    }

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
            return class_basename($this);
        }

        if ($name == 'aliases' && empty($this->aliases)) {
            return [mb_strtolower(class_basename($this))];
        }

        return $this->{$name};
    }
}
