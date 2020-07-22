<?php

namespace AsemAlalami\LaravelAdvancedFilter;

use AsemAlalami\LaravelAdvancedFilter\Operators\Operator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

trait HasFilter
{
    use Filterable;

    protected $original = [];
    protected $fields = [];
    protected $fieldsAliases = [];

    public abstract function setup();

    /**
     * Filter By request
     *
     * @param Builder $builder
     * @param Request|array|null $request
     * @param Filter|null $filter
     */
    public function scopeFilter(Builder $builder, $request = null, Filter $filter = null)
    {
        $this->apply($builder, $request);
    }

    /**
     * @param Field|string $field
     */
    public function addField($field)
    {
        $this->original[] = $field;
    }

    public function resolveFields()
    {
        foreach ($this->original as $field) {
            if (is_string($field)) {
                $this->fields[$field] = new Field($field, $this->getFieldDatatype($field), ['Equal', 'NotEqual'],
                    $field);
            } else {
                $this->fields[$field->name] = $field;

                $this->fieldsAliases[$field->alias] = $field->name;
            }
        }
    }

    public function getFieldDatatype($field)
    {
        $fieldName = is_string($field) ? $field : $field->name;

        if ($this instanceof Model) {
            return $this->hasCast($fieldName) ? $this->getCastType($fieldName) : 'string';
        }

        // TODO: get casting from custom Filter

        // TODO: get casting from Builder (getModel)

        return 'string';
    }

    public function apply(Builder $builder, $request)
    {
        $filterRequest = FilterRequest::createFromRequest($request);

        foreach ($filterRequest->getFilters() as $filter) {
            if ($field = $this->getFilterableField($filter['field'])) {
                $operator = $this->operatorAliases[$filter['operator']];
                $value = $filter['value'];

                $value = $this->hasCast($field->name) ? $this->castAttribute($field->name, $value) : $value;

                if ($field->isRelation()) {
                    $function = $filterRequest->getConjunction() == 'and' ? 'where' : 'orWhere';

                    $builder = $builder->{$function . 'Has'}(
                        $field->getRelation(),
                        function (Builder $builder) use ($field, $value, $operator) {
                            return $builder->{Operator::getFunction($operator)}($field->getColumn(), $value);
                        }
                    );
                } else {
                    $builder = $builder->{Operator::getFunction($operator)}(
                        $field->getColumn(),
                        $value,
                        $filterRequest->getConjunction()
                    );
                }
            }
        }
    }

    /**
     * @param string $field
     *
     * @return Field|bool
     */
    private function getFilterableField(string $field)
    {
        if (array_key_exists($field, $this->fields)) {
            return $this->fields[$field];
        }

        if (array_key_exists($field, $this->fieldsAliases)) {
            return $this->fields[$this->fieldsAliases[$field]];
        }

        return false;
    }

    public function initializeHasFilter()
    {
        $this->setup();

        $this->resolveFields();
    }
}
