<?php

namespace AsemAlalami\LaravelAdvancedFilter;

use AsemAlalami\LaravelAdvancedFilter\Fields\Field;
use AsemAlalami\LaravelAdvancedFilter\Fields\HasFields;
use AsemAlalami\LaravelAdvancedFilter\Operators\Operator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Trait HasFilter
 * @package AsemAlalami\LaravelAdvancedFilter
 *
 * @method Builder|$this filter(Request|array $request = null, Filter $filter = null)
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
     * @param Request|array|null $request
     * @param Filter|null $filter
     *
     * @return Builder
     */
    public function scopeFilter(Builder $builder, $request = null, Filter $filter = null)
    {
        return $this->apply($builder, $request);
    }

    /**
     * Filter fields from request
     *
     * @param Builder $builder
     * @param $request
     *
     * @return Builder
     */
    private function apply(Builder $builder, $request)
    {
        $filterRequest = FilterRequest::createFromRequest($request);
        $conjunction = $filterRequest->getConjunction();

        foreach ($filterRequest->getFilters() as $filter) {
            if ($field = $this->getFilterableField($filter['field'])) {

                $operator = $this->getOperatorFromAliases($filter['operator']);
                $value = $field->getCastedValue($filter['value']);

                // don't filter if the operator is in excepted operators
                if (!$field->isAllowedOperator($operator)) {
                    continue;
                }

                // apply filter inside relation if the field from relation
                if ($field->isFromRelation()) {
                    // apply on custom scope if the relation has scope
                    if ($this->modelHasScope($builder, $field->getScopeRelationFunctionName())) {
                        $builder = $builder->{$field->getScopeRelationFunctionName()}($field, $operator, $value, $conjunction);
                    } else {
                        $builder = $builder->has($field->getRelation(), '>=', 1, $conjunction,
                            function (Builder $builder) use ($field, $value, $operator) {
                                return $this->filterField($builder, $field, $operator, $value);
                            }
                        );
                    }
                } else {
                    // apply on field
                    $builder = $this->filterField($builder, $field, $operator, $value, $conjunction);
                }
            }
        }

        return $builder;
    }

    /**
     * Filter field
     *
     * check if the field has a custom scope and apply it
     *
     * @param Builder $builder
     * @param Field $field
     * @param $operator
     * @param $value
     * @param string $conjunction
     *
     * @return mixed
     */
    private function filterField(Builder $builder, Field $field, $operator, $value, $conjunction = 'and')
    {
        // apply on custom scope if the field has scope
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
