<?php


namespace AsemAlalami\LaravelAdvancedFilter\Fields;


use AsemAlalami\LaravelAdvancedFilter\Exceptions\DatatypeNotFoundException;
use Illuminate\Support\Str;

trait HasFields
{
    /** @var FieldsFactory[] */
    private $fieldsFactories = [];
    /** @var Field[] */
    protected $fields = [];
    /** @var string[] */
    protected $fieldsAliases = [];
    protected $generalSearch = ['fields' => [], 'operator' => null];

    /**
     * Add a normal/relational field
     *
     * @param string $field
     * @param string|null $alias
     * @param bool|null $inRelation
     *
     * @return Field
     */
    public function addField(string $field, string $alias = null, ?bool $inRelation = null)
    {
        $field = new Field($this, $field, $alias, $inRelation);

        $this->fields[$field->name] = $field;

        return $field;
    }

    /**
     * Add normal/relational fields
     *
     * @param $fields
     *
     * @return FieldsFactory
     */
    public function addFields($fields)
    {
        $fieldFactory = new FieldsFactory($this, $fields);

        $this->fieldsFactories[] = $fieldFactory;

        return $fieldFactory;
    }

    /**
     * Add a count field
     *
     * @param string $relation
     * @param string|null $alias
     * @param callable|null $callback
     *
     * @throws DatatypeNotFoundException
     */
    public function addCountField(string $relation, string $alias = null, callable $callback = null)
    {
        $field = new Field($this, "{$relation}.", $alias ?: $this->getCountFieldAlias($relation));

        $this->fields[$field->name] = $field->setDatatype('numeric')->setCountCallback($callback);
    }

    /**
     * Add a custom field
     *
     * @param string $alias
     * @param string $sqlRaw
     * @param null $relation
     *
     * @return Field
     */
    public function addCustomField(string $alias, string $sqlRaw, $relation = null)
    {
        $field = new Field($this, $relation ? "{$relation}.{$alias}" : $alias, $alias);

        $this->fields[$field->name] = $field->setCustomSqlRaw($sqlRaw);

        return $field;
    }

    public function addGeneralSearch(array $fields, string $operator = null)
    {
        $this->generalSearch = ['fields' => $fields, 'operator' => $operator];
    }

    /**
     * Resolve factories fields and set fields aliases
     *
     * @throws DatatypeNotFoundException
     */
    private function resolveFields()
    {
        // add fields from factories to fields
        foreach ($this->fieldsFactories as $fieldsFactory) {
            foreach ($fieldsFactory->getFields() as $field) {
                if (!array_key_exists($field->name, $this->fields)) {
                    $this->fields[$field->name] = $field;
                }
            }
        }

        // set aliases fields
        foreach ($this->fields as $field) {
            $this->fieldsAliases[$field->alias] = $field->name;
        }
    }

    /**
     * Determined if the alias exists in filterable fields
     *
     * @param string $alias
     *
     * @return Field|bool <FALSE> the alias does not exist
     */
    protected function getFilterableField(string $alias)
    {
        if (array_key_exists($alias, $this->fieldsAliases)) {
            return $this->fields[$this->fieldsAliases[$alias]];
        }

        return false;
    }

    /**
     * Get default alias for count field from a relation
     *
     * @param string $relation
     *
     * @return string
     */
    private function getCountFieldAlias(string $relation)
    {
        return Str::snake($relation) . '_count';
    }
}
