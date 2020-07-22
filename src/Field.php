<?php


namespace AsemAlalami\LaravelAdvancedFilter;


class Field
{
    /** @var string $name */
    public $name;
    /** @var string $datatype */
    public $datatype;
    /** @var array $operators */
    public $operators;
    /** @var string $alias */
    public $alias;
    /** @var false|string[] $nameExploded */
    private $nameExploded;

    /**
     * Field constructor.
     * @param $name
     * @param $datatype
     * @param $operators
     * @param $alias
     */
    public function __construct(string $name, string $datatype, array $operators, string $alias)
    {
        $this->name = $name;
        $this->datatype = $datatype;
        $this->operators = $operators;
        $this->alias = $alias;
        $this->nameExploded = explode('.', $this->name);
    }

    public function isRelation()
    {
        return count($this->nameExploded) > 1;
    }

    public function getRelation()
    {
        return implode('.', array_slice($this->nameExploded, 0, count($this->nameExploded) - 1));
    }

    public function getColumn()
    {
        return array_slice($this->nameExploded, -1)[0];
    }
}
