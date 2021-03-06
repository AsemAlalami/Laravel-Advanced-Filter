<?php


namespace AsemAlalami\LaravelAdvancedFilter;


use AsemAlalami\LaravelAdvancedFilter\QueryFormats\QueryFormat;
use Illuminate\Http\Request;

class FilterRequest
{
    private $filters = [];
    private $conjunction = 'and';
    private $generalSearch = null;

    public function addFilter(string $fieldName, string $operator, $value = null)
    {
        $this->filters[] = ['field' => $fieldName, 'operator' => $operator, 'value' => $value];

        return $this;
    }

    public function getFilters()
    {
        return $this->filters;
    }

    public function setConjunction(string $conjunction)
    {
        $this->conjunction = $conjunction;

        return $this;
    }

    public function getConjunction()
    {
        return $this->conjunction;
    }

    /**
     * @return null
     */
    public function getGeneralSearch()
    {
        return $this->generalSearch;
    }

    /**
     * @param string|null $generalSearch
     */
    public function setGeneralSearch(?string $generalSearch): void
    {
        $this->generalSearch = $generalSearch;
    }

    public static function createFromRequest(Request $request = null)
    {
        $request = $request ?: request();

        $filterRequest = QueryFormat::factory($request);
        $filterRequest->setConjunction($filterRequest->getConjunctionFromRequest($request));
        $filterRequest->setGeneralSearch($filterRequest->getGeneralSearchFromRequest($request));

        return $filterRequest;
    }

    private function getConjunctionFromRequest(Request $request = null)
    {
        $paramConjunctionName = config('advanced_filter.param_conjunction_name', 'conjunction');
        $defaultConjunction = config('advanced_filter.default_conjunction', 'and');

        return $request ?
            $request->input($paramConjunctionName, $defaultConjunction) :
            request($paramConjunctionName, $defaultConjunction);
    }

    private function getGeneralSearchFromRequest(Request $request = null)
    {
        $paramGeneralSearchName = config('advanced_filter.param_general_search_name', 'query');

        return $request ?
            $request->input($paramGeneralSearchName) :
            request($paramGeneralSearchName);
    }
}
