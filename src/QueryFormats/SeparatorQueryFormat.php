<?php


namespace AsemAlalami\LaravelAdvancedFilter\QueryFormats;


use AsemAlalami\LaravelAdvancedFilter\FilterRequest;
use Illuminate\Support\Str;

class SeparatorQueryFormat extends QueryFormat
{

    public function format($filters): FilterRequest
    {
        $prefix = config('advanced_filter.param_filter_name', 'filters');
        $separator = $this->getSeparatorFromFormat();

        $requestFilter = new FilterRequest();

        $filter = [];
        foreach ($filters as $paramName => $paramValue) {
            if (Str::startsWith($paramName, "{$prefix}{$separator}")) {
                $paramExploded = explode($separator, $paramName);

                $fieldName = $paramExploded[1] ?? null;
                if ($fieldName) {
                    if (!isset($filter[$fieldName])) {
                        $filter[$fieldName] = [];
                    }

                    if ($paramExploded[2] == $this->fieldParams['operator']) {
                        $filter[$fieldName]['operator'] = $paramName;
                    } elseif ($paramExploded[2] == $this->fieldParams['value']) {
                        $filter[$fieldName]['value'] = $paramName;
                    }
                }
            }
        }

        foreach ($filter as $fieldName => $item) {
            $operator = empty($item['operator']) ? $this->defaultOperator : $item['operator'];
            $value = $item['value'] ?? null;

            $requestFilter->addFilter($fieldName, $operator, $value);
        }

        return $requestFilter;
    }

    private function getSeparatorFromFormat()
    {
        $queryFormat = config('advanced_filter.query_format', 'json');
        $defaultSeparator = '^';

        return (explode("separator:", $queryFormat)[1] ?? $defaultSeparator) ?: $defaultSeparator;
    }

}
