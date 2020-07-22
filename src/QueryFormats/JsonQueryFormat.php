<?php


namespace AsemAlalami\LaravelAdvancedFilter\QueryFormats;


use AsemAlalami\LaravelAdvancedFilter\FilterRequest;

class JsonQueryFormat extends QueryFormat
{

    public function format($filters): FilterRequest
    {
        $requestFilter = new FilterRequest();

        $filters = json_decode($filters, true);

        foreach ($filters ?: [] as $filter) {
            $fieldName = $filter[$this->fieldParams['field']] ?? null;
            if ($fieldName) {
                $operator = $filter[$this->fieldParams['operator']] ?? $this->defaultOperator;
                $value = $filter[$this->fieldParams['value']] ?? null;

                $requestFilter->addFilter($fieldName, $operator, $value);
            }
        }

        return $requestFilter;
    }

}
