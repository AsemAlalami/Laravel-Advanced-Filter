<?php


namespace AsemAlalami\LaravelAdvancedFilter\QueryFormats;


use AsemAlalami\LaravelAdvancedFilter\FilterRequest;
use Illuminate\Http\Request;

abstract class QueryFormat
{
    const QUERY_FORMAT_JSON = 'json';
    const QUERY_FORMAT_ARRAY = 'array';
    const QUERY_FORMAT_SEPARATOR = 'separator';

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
        $filterName = config('advanced_filter.param_filter_name', 'filters');

        switch (static::getQueryFormat()) {
            case self::QUERY_FORMAT_JSON:
                return (new JsonQueryFormat())->format($request->input($filterName, '[]'));
            case self::QUERY_FORMAT_SEPARATOR:
                return (new SeparatorQueryFormat())->format($request->all());
            case self::QUERY_FORMAT_ARRAY:
                return (new ArrayQueryFormat())->format($request->input($filterName, []));
        }

        throw new \InvalidArgumentException('must be a valid query format');
    }

    public static function getQueryFormat()
    {
        return substr(config('advanced_filter.query_format', self::QUERY_FORMAT_JSON), 0, 9);
    }
}
