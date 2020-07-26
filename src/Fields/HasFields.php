<?php


namespace AsemAlalami\LaravelAdvancedFilter\Fields;


use Illuminate\Support\Str;

trait HasFields
{
    /** @var FieldsFactory[] */
    private $fieldsFactories = [];
    /** @var Field[] */
    protected $fields = [];
    /** @var string[] */
    protected $fieldsAliases = [];
    protected $countFields = [];

    /**
     * @param string $field
     * @param string $alias
     *
     * @return Field
     */
    public function addField(string $field, string $alias = null)
    {
        $field = new Field($this, $field, $alias);

        $this->fields[$field->name] = $field;

        return $field;
    }

    public function addFields($fields)
    {
        $fieldFactory = new FieldsFactory($this, $fields);

        $this->fieldsFactories[] = $fieldFactory;

        return $fieldFactory;
    }

    public function addCountField(string $relation, string $alias = null, callable $callback = null)
    {
        $field = new Field($this, "{$relation}.", $alias ?: $this->getCountFieldAlias($relation));

        $this->fields[$field->name] = $field->setDatatype('numeric')->setCountCallback($callback);
    }

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
     * @param string $alias
     *
     * @return Field|bool
     */
    protected function getFilterableField(string $alias)
    {
        if (array_key_exists($alias, $this->fieldsAliases)) {
            return $this->fields[$this->fieldsAliases[$alias]];
        }

        return false;
    }

    private function getCountFieldAlias(string $relation)
    {
        return Str::snake($relation) . '_count';
    }
}
