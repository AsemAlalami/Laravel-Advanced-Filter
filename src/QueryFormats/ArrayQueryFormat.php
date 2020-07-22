<?php


namespace AsemAlalami\LaravelAdvancedFilter\QueryFormats;


use AsemAlalami\LaravelAdvancedFilter\FilterRequest;

class ArrayQueryFormat extends QueryFormat
{

    public function format($filters): FilterRequest
    {
        $requestFilter = new FilterRequest();

        foreach ($filters as $fieldName => $filter) {
            if (!is_int($fieldName)) {
                $operator = $filter[$this->fieldParams['operator']] ?? $this->defaultOperator;
                $value = $filter[$this->fieldParams['value']] ?? null;

                $requestFilter->addFilter($fieldName, $operator, $value);
            }
        }

        return $requestFilter;
    }
}
