<?php


namespace AsemAlalami\LaravelAdvancedFilter\QueryFormats;


use AsemAlalami\LaravelAdvancedFilter\FilterRequest;
use Illuminate\Http\Request;

abstract class QueryFormat
{
    /** @var array $fieldParams */
    public $fieldParams;
    /** @var string $defaultOperator */
    public $defaultOperator;

    public function __construct()
    {
        $this->fieldParams = config('advanced_filter.field_params');
        $this->defaultOperator = config('advanced_filter.default_operator');
    }

    public abstract function format($filters): FilterRequest;

    public static function factory(Request $request)
    {
        if ($custom = static::loadCustomQueryFormat($request)) {
            return $custom;
        }

        return static::loadQueryFormat($request);
    }

    private static function loadCustomQueryFormat(Request $request)
    {
        $customQueryFormat = config('advanced_filter.custom_query_format');

        if (!empty($customQueryFormat)) {
            try {
                $customFormat = new $customQueryFormat();
                if ($customFormat instanceof QueryFormat) {
                    return $customFormat->format($request);
                }
            } catch (\Exception $exception) {
                throw new \InvalidArgumentException('must be a valid query format');
            }
        }

        return false;
    }

    private static function loadQueryFormat(Request $request)
    {
        $queryFormat = config('advanced_filter.query_format', 'json');
        $filterName = config('advanced_filter.param_filter_name', 'filters');

        switch (substr($queryFormat, 0, 9)) {
            case 'json':
                return (new JsonQueryFormat())->format($request->input($filterName, '[]'));
            case 'separator':
                return (new SeparatorQueryFormat())->format($request->all());
            case 'array':
                return (new ArrayQueryFormat())->format($request->input($filterName, []));
        }

        throw new \InvalidArgumentException('must be a valid query format');
    }
}
