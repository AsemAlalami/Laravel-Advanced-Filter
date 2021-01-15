<?php

namespace AsemAlalami\LaravelAdvancedFilter;

use AsemAlalami\LaravelAdvancedFilter\Exceptions\UnsupportedDriverException;
use AsemAlalami\LaravelAdvancedFilter\Fields\Field;
use AsemAlalami\LaravelAdvancedFilter\Fields\HasFields;
use AsemAlalami\LaravelAdvancedFilter\Operators\Operator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Trait HasFilter
 * @package AsemAlalami\LaravelAdvancedFilter
 *
 * @method static Builder|static filter(Request|array $request = null, Filter $filter = null)
 * @see HasFilter::scopeFilter
 */
trait HasFilter
{
    use Filterable, HasFields;

    public abstract function setupFilter();

    /**
     * Filter By request
     *
     * @param Builder $builder
     * @param Request|null $request
     * @param Filter|null $filter
     *
     * @return Builder
     * @throws UnsupportedDriverException
     */
    public function scopeFilter(Builder $builder, Request $request = null, Filter $filter = null)
    {
        $filterRequest = FilterRequest::createFromRequest($request);

        $builder = $this->apply($builder, $filterRequest);

        $builder = $this->applyGeneralSearch($builder, $filterRequest);

        return $builder;
    }

    /**
     * Filter fields from request
     *
     * @param Builder $builder
     * @param FilterRequest $filterRequest
     *
     * @return Builder
     * @throws UnsupportedDriverException
     */
    private function apply(Builder $builder, FilterRequest $filterRequest)
    {
        $conjunction = $filterRequest->getConjunction();

        foreach ($filterRequest->getFilters() as $filter) {
            if ($field = $this->getFilterableField($filter['field'])) {

                $operator = $this->getOperatorFromAliases($filter['operator']);
                $value = $field->getCastedValue($filter['value']);

                // don't filter if the operator is in excepted operators
                if (!$field->isAllowedOperator($operator)) {
                    continue;
                }

                $builder = $this->filterField($builder, $field, $operator, $value, $conjunction);
            }
        }

        return $builder;
    }

    /**
     * Filter general search from the request
     *
     * @param Builder $builder
     * @param FilterRequest $filterRequest
     *
     * @return Builder
     * @throws UnsupportedDriverException
     */
    private function applyGeneralSearch(Builder $builder, FilterRequest $filterRequest)
    {
        if (!empty($filterRequest->getGeneralSearch())) {
            $operator = $this->generalSearch['operator'] ?: config('advanced_filter.default_general_search_operator');

            $builder->where(function (Builder $builder) use ($filterRequest, $operator) {
                foreach ($this->generalSearch['fields'] as $fieldName) {
                    $field = new Field($builder->getModel(), $fieldName);

                    $this->filterField($builder, $field, $operator, $filterRequest->getGeneralSearch(), 'or');
                }
            });
        }

        return $builder;
    }

    /**
     * Filter a field
     *
     * @param Builder $builder
     * @param Field $field
     * @param $operator
     * @param $value
     * @param string $conjunction
     *
     * @return Builder
     * @throws UnsupportedDriverException
     */
    private function filterField(Builder $builder, Field $field, $operator, $value, $conjunction = 'and')
    {
        // apply the filter inside the relation if the field from relation
        if ($field->isFromRelation()) {
            // apply on custom scope if the relation has a scope
            if ($this->modelHasScope($builder, $field->getScopeRelationFunctionName())) {
                return $builder->{$field->getScopeRelationFunctionName()}($field, $operator, $value, $conjunction);
            } else {
                if ($builder->getConnection()->getName() == 'mongodb') {
                    throw new UnsupportedDriverException('MongoDB', 'relational');
                }

                return $builder->has($field->getRelation(), '>=', 1, $conjunction,
                    function (Builder $builder) use ($field, $value, $operator) {
                        // consider as a non relation field inside the relation
                        return $this->filterNonRelationalField($builder, $field, $operator, $value);
                    }
                );
            }
        } else {
            // a non relational field
            return $this->filterNonRelationalField($builder, $field, $operator, $value, $conjunction);
        }
    }

    /**
     * Filter a non relational field
     *
     * it checks if the field has a custom scope
     *
     * @param Builder $builder
     * @param Field $field
     * @param $operator
     * @param $value
     * @param string $conjunction
     *
     * @return mixed
     */
    private function filterNonRelationalField(Builder $builder, Field $field, $operator, $value, $conjunction = 'and')
    {
        // apply on custom scope if the field has a scope
        if ($this->modelHasScope($builder, $field->getScopeFunctionName())) {
            return $builder->{$field->getScopeFunctionName()}($field, $operator, $value, $conjunction);
        }

        return $builder->{Operator::getFunction($operator)}($field, $value, $conjunction);
    }

    /**
     * Determine if the given model has a scope.
     *
     * @param Builder $builder
     * @param $scopeName
     * @return bool
     */
    private function modelHasScope(Builder $builder, $scopeName)
    {
        return $builder->getModel() && method_exists($builder->getModel(), 'scope' . ucfirst($scopeName));
    }

    public function initializeHasFilter()
    {
        $this->setupFilter();

        $this->resolveFields();
    }
}
