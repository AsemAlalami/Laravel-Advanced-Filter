<?php


namespace AsemAlalami\LaravelAdvancedFilter\Fields;


use AsemAlalami\LaravelAdvancedFilter\Exceptions\DatatypeNotFound;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Field
{
    use FieldCast;

    /** @var Model $model */
    private $model;
    /** @var string $name */
    public $name;
    /** @var string $datatype */
    private $datatype;
    /** @var string $alias */
    public $alias;
    /** @var false|string[] $nameExploded */
    private $nameExploded;
    /** @var callable $countCallback */
    public $countCallback = null;
    /** @var string $customSqlRaw */
    public $customSqlRaw;
    /** @var array|string[] $exceptOperators */
    public $exceptOperators;

    /**
     * Field constructor.
     * @param Model $model
     * @param string $name
     * @param string $alias
     */
    public function __construct($model, string $name, string $alias = null)
    {
        $this->model = $model;
        $this->name = $name;
        $this->alias = $alias ?: $name;
        $this->nameExploded = explode('.', $this->name);
    }

    public function isFromRelation()
    {
        return count($this->nameExploded) > 1 && !$this->isCount();
    }

    public function getRelation()
    {
        return implode('.', array_slice($this->nameExploded, 0, count($this->nameExploded) - 1));
    }

    public function getColumn()
    {
        return $this->isCustom() ? $this->customSqlRaw : (array_slice($this->nameExploded, -1)[0] ?: null);
    }

    public function isCount()
    {
        return is_null($this->getColumn());
    }

    public function isCustom()
    {
        return !empty($this->customSqlRaw);
    }

    /**
     * @param string|null $datatype
     *
     * @return $this
     * @throws DatatypeNotFound
     */
    public function setDatatype(?string $datatype)
    {
        if (!empty($datatype)) {
            if (in_array($datatype, static::$primitiveDatatypes)) {
                $this->datatype = $datatype;
            } else {
                throw new DatatypeNotFound($datatype);
            }
        }


        return $this;
    }

    public function setCountCallback(?callable $callback)
    {
        $this->countCallback = $callback;

        return $this;
    }

    public function setCustomSqlRaw(string $sqlRaw)
    {
        $this->customSqlRaw = $sqlRaw;

        return $this;
    }

    /**
     * @param array|string|string[] $operators
     *
     * @return $this
     */
    public function setExceptedOperators($operators)
    {
        $this->exceptOperators = Arr::wrap($operators);;

        return $this;
    }

    public function getDatatype()
    {
        // TODO: cache (as static) result to prevent calculate it again

        if (empty($this->datatype)) {
            return $this->getFieldCastType($this->name, $this->getModel()->getCasts());
        }

        return $this->datatype;
    }

    /**
     * @return Model
     */
    public function getModel()
    {
        $model = $this->model;

        if ($this->isFromRelation()) {
            foreach (explode('.', $this->getRelation()) as $relation) {
                $model = $model->{$relation}()->getRelated();
            }
        }

        return $model;
    }

    /**
     * Determined if the operator does not belong to excepted operators
     *
     * @param string $operator
     *
     * @return bool
     */
    public function isAllowedOperator(string $operator)
    {
        return !in_array($operator, $this->exceptOperators);
    }

    public function getScopeFunctionName()
    {
        $prefix = config('advanced_filter.prefix_scope_function', 'where');
        $column = ucfirst(Str::camel($this->getColumn()));

        return "{$prefix}{$column}";
    }

    public function getScopeRelationFunctionName()
    {
        $prefix = config('advanced_filter.prefix_scope_function', 'where');
        $relation = ucfirst(Str::camel(str_replace('.', '_', $this->getRelation())));

        return "{$prefix}{$relation}";
    }
}
