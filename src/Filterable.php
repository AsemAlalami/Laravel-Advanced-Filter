<?php

namespace AsemAlalami\LaravelAdvancedFilter;

use AsemAlalami\LaravelAdvancedFilter\Exceptions\OperatorNotFound;
use AsemAlalami\LaravelAdvancedFilter\Fields\Field;
use AsemAlalami\LaravelAdvancedFilter\Operators\Operator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

/**
 * Trait Filterable
 * @package AsemAlalami\LaravelAdvancedFilter
 *
 * @method Builder|$this filter(Request|array $request = null, Filter $filter = null)
 * @see Filterable::scopeFilter
 */
trait Filterable
{
    protected $operatorAliases = [];

    /**
     * Bind operators
     *
     * @return $this
     * @throws OperatorNotFound
     */
    private function bindOperators()
    {
        $operators = config('advanced_filter.operators', []);

        foreach ($operators ?: [] as $operator => $aliases) {
            if (is_int($operator)) {
                $operator = $aliases;
            }

            $operatorClass = $this->getOperatorsNamespace() . $operator; // operator class path

            try {
                /** @var Operator $operator */
                $operator = new $operatorClass;
                $operator->setAliases(Arr::wrap($aliases));

                $this->bindOperator($operator);
            } catch (\Error $exception) {
                throw new OperatorNotFound($operator);
            }
        }

        return $this;
    }

    /**
     * @param Operator $operator
     */
    private function bindOperator(Operator $operator)
    {
        Builder::macro(Operator::getFunction($operator->name), function (...$parameters) use ($operator) {
            // convert string field to Field class
            $field = $parameters[0];
            if (is_string($parameters[0])) {
                $field = new Field($this->getModel(), $field);

                $parameters[0] = $field;
            }

            // push Builder on first index
            array_unshift($parameters, $this);

            return $operator->execute(...$parameters);
        });

        foreach ($operator->aliases as $alias) {
            $this->operatorAliases[$alias] = $operator->name;
        }
    }

    private function getOperatorsNamespace()
    {
        return __NAMESPACE__ . '\\Operators\\';
    }

    public function getOperatorFromAliases(string $operatorAlias)
    {
        return array_key_exists($operatorAlias, $this->operatorAliases) ?
            $this->operatorAliases[$operatorAlias] :
            config('advanced_filter.default_operator', 'Equal');
    }

    public function initializeFilterable()
    {
        $this->bindOperators();
    }
}
