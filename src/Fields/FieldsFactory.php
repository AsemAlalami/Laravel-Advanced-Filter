<?php


namespace AsemAlalami\LaravelAdvancedFilter\Fields;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class FieldsFactory
{
    /** @var Model $model */
    private $model;
    private $fields = [];
    private $datatype;
    private $operators;

    /**
     * FieldsFactory constructor.
     * @param Model $model
     * @param null $fields
     */
    public function __construct($model, $fields = null)
    {
        $this->model = $model;

        $this->setFields($fields);
    }

    /**
     * @param array|string $fields
     *
     * @return $this
     */
    public function setFields($fields)
    {
        $fields = Arr::wrap($fields);

        foreach ($fields as $field => $alias) {
            if (is_int($field)) {
                $field = $alias;
            }

            $this->fields[$field] = $alias;
        }

        return $this;
    }

    /**
     * @param string $datatype
     *
     * @return FieldsFactory
     */
    public function setDatatype(?string $datatype)
    {
        $this->datatype = $datatype;

        return $this;
    }

    /**
     * @param array|string $operators
     *
     * @return $this
     */
    public function setOperators($operators)
    {
        $this->operators = Arr::wrap($operators);

        return $this;
    }

    /**
     * @return Field[]
     */
    public function getFields()
    {
        $fields = [];
        foreach ($this->fields as $field => $alias) {
            $fields[] = (new Field($this->model, $field, $alias))
                ->setOperators($this->operators)
                ->setDatatype($this->datatype);
        }

        return $fields;
    }
}
