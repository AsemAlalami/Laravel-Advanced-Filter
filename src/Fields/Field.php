<?php


namespace AsemAlalami\LaravelAdvancedFilter\Fields;


use AsemAlalami\LaravelAdvancedFilter\Exceptions\DatatypeNotFound;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Field
{
    use FieldCast;

    /** @var Model $model */
    private $model;
    /** @var string $name */
    public $name;
    /** @var string $datatype */
    private $datatype;
    /** @var array $operators */
    private $operators;
    /** @var string $alias */
    public $alias;
    /** @var false|string[] $nameExploded */
    private $nameExploded;

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
        return count($this->nameExploded) > 1;
    }

    public function getRelation()
    {
        return implode('.', array_slice($this->nameExploded, 0, count($this->nameExploded) - 1));
    }

    public function getColumn()
    {
        return array_slice($this->nameExploded, -1)[0];
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

    public function setOperators($operators)
    {
        $this->operators = Arr::wrap($operators);

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

    public function getOperators()
    {
        if (empty($this->operators)) {
            return Arr::wrap(config("advanced_filter.data_types.{$this->getDataType()}"));
        }

        return $this->operators;
    }
}
