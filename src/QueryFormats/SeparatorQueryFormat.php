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

        // convert string parameters to array
        $parameters = [];
        foreach ($filters as $paramName => $paramValue) {
            if (Str::startsWith($paramName, "{$prefix}{$separator}")) {
                $stringArray = $this->stringBySeparatorToStringArray($paramName, $separator);
                $stringArray .= "={$paramValue}"; // add the parameter value
                parse_str($stringArray, $filter); // parse to array

                $parameters = array_merge_recursive($parameters, $filter);
            }
        }

        // use ArrayQueryFormat
        return (new ArrayQueryFormat())->format($parameters[$prefix]);
    }

    private function getSeparatorFromFormat()
    {
        $queryFormat = config('advanced_filter.query_format', 'json');
        $defaultSeparator = '^';

        return (explode("separator:", $queryFormat)[1] ?? $defaultSeparator) ?: $defaultSeparator;
    }

    /**
     * Convert string separated to string array
     *
     * @param string $string
     * @param string $separator
     *
     * @return string
     */
    private function stringBySeparatorToStringArray(string $string, string $separator)
    {
        $stringArray = '';
        foreach (explode($separator, $string) as $index => $item) {
            if ($index == 0) {
                $stringArray .= "{$item}";
            } else {
                $stringArray .= "[{$item}]";
            }
        }

        return $stringArray;
    }

}
