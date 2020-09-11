<?php


namespace AsemAlalami\LaravelAdvancedFilter\Operators;


use AsemAlalami\LaravelAdvancedFilter\Fields\Field;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Operator
 * @package AsemAlalami\LaravelAdvancedFilter\Operators
 *
 * @property string $name
 * @property array $aliases
 */
abstract class Operator
{
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
     * Get the equivalent operator in the SQL
     *
     * @return string
     */
    public abstract function getSqlOperator(): string;

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
        return $builder->has($field->getRelation(), $this->getSqlOperator(), $value, $conjunction, $field->countCallback);
    }

    /**
     * The function calls when trying to apply the operator on a custom field
     *
     * @param Builder $builder
     * @param Field $field
     * @param $value
     * @param string $conjunction
     *
     * @return Builder
     */
    public function applyOnCustom(Builder $builder, Field $field, $value, string $conjunction = 'and'): Builder
    {
        $sql = "{$field->getColumn()} {$this->getSqlOperator()} {$this->getSqlValue($value)}";

        return $builder->whereRaw($sql, [], $conjunction);
    }

    /**
     * Apply the operator on the field depends on field type
     *
     * @param Builder $builder
     * @param Field $field
     * @param $value
     * @param string $conjunction
     *
     * @return Builder
     */
    public function execute(Builder $builder, Field $field, $value, string $conjunction = 'and'): Builder
    {
        // if the field is custom, call applyOnCustom function
        if ($field->isCustom()) {
            return $this->applyOnCustom($builder, $field, $value, $conjunction);
        }

        // if the field is count, call applyOnCount function
        if ($field->isCount()) {
            return $this->applyOnCount($builder, $field, $value, $conjunction);
        }

        return $this->apply($builder, $field, $value, $conjunction);
    }

    protected function getSqlValue($value)
    {
        return $value;
    }

    public static function getFunction($operatorName)
    {
        $prefixOperatorFunction = config('advanced_filter.prefix_operator_function', 'filterWhere');
        $operatorName = ucfirst($operatorName);

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
