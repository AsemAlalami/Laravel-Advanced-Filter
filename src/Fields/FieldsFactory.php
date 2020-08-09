<?php


namespace AsemAlalami\LaravelAdvancedFilter\Fields;


use AsemAlalami\LaravelAdvancedFilter\Exceptions\DatatypeNotFound;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class FieldsFactory
{
    /** @var Model $model */
    private $model;
    private $fields = [];
    private $datatype;
    private $exceptedOperators;

    /**
     * FieldsFactory constructor.
     *
     * @param Model $model
     * @param string|string[]|null $fields
     */
    public function __construct($model, $fields = null)
    {
        $this->model = $model;

        $this->setFields($fields);
    }

    /**
     * @param string|string[] $fields
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
     * @param string|null $datatype
     *
     * @return FieldsFactory
     */
    public function setDatatype(?string $datatype)
    {
        $this->datatype = $datatype;

        return $this;
    }

    /**
     * @param string|string[] $exceptedOperators
     *
     * @return $this
     */
    public function setExceptedOperators($exceptedOperators)
    {
        $this->exceptedOperators = Arr::wrap($exceptedOperators);

        return $this;
    }

    /**
     * @return Field[]
     * @throws DatatypeNotFound
     */
    public function getFields()
    {
        $fields = [];
        foreach ($this->fields as $field => $alias) {
            $fields[] = (new Field($this->model, $field, $alias))
                ->setExceptedOperators($this->exceptedOperators)
                ->setDatatype($this->datatype);
        }

        return $fields;
    }
}
