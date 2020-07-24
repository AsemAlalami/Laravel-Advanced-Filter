<?php

namespace AsemAlalami\LaravelAdvancedFilter;

use AsemAlalami\LaravelAdvancedFilter\Fields\HasFields;
use AsemAlalami\LaravelAdvancedFilter\Operators\Operator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait HasFilter
{
    use Filterable, HasFields;

    public abstract function setupFilter();

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

    public function apply(Builder $builder, $request)
    {
        $filterRequest = FilterRequest::createFromRequest($request);

        foreach ($filterRequest->getFilters() as $filter) {
            if ($field = $this->getFilterableField($filter['field'])) {

                $operator = $this->getOperatorFromAliases($filter['operator']);
                $value = $field->getCastedValue($filter['value']);

                if ($field->isFromRelation()) {
                    $function = $filterRequest->getConjunction() == 'and' ? 'where' : 'orWhere';

                    $builder = $builder->{$function . 'Has'}(
                        $field->getRelation(),
                        function (Builder $builder) use ($field, $value, $operator) {
                            return $builder->{Operator::getFunction($operator)}($field, $value);
                        }
                    );
                } else {
                    $builder = $builder->{Operator::getFunction($operator)}(
                        $field,
                        $value,
                        $filterRequest->getConjunction()
                    );
                }
            }
        }
    }

    public function initializeHasFilter()
    {
        $this->setupFilter();

        $this->resolveFields();
    }
}
