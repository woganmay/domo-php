<?php

namespace WoganMay\DomoPHP;

class PDPFilterBuilder
{
    private $filters;
    private $_column = null;

    public function __construct()
    {
        $this->filters = [];
    }

    public function toArray()
    {
        // Publish the array
        return $this->filters;
    }

    /**
     * @param string $operator The name of the Operator
     * @param array $values An array of values
     * @param bool $not TRUE to invert the filter
     * @throws \Exception
     */
    private function _add($operator, $values, $not = false)
    {

        if ($this->_column == null)
            throw new \Exception("Can't add a filter without a preceding column()");

        if (!is_array($values)) $values = [$values];

        $add = [
            'operator' => $operator,
            'column' => $this->_column,
            'values' => $values
        ];

        if ($not) $add['not'] = true;

        $this->filters[] = $add;
        $this->_column = null;
    }

    /**
     * @param $column The (case-sensitive) name of the column to apply the filter to
     * @return $this
     */
    public function column($column)
    {
        $this->_column = $column;
        return $this;
    }

    public function equals($values)
    {
        $this->_add("EQUALS", $values);
    }

    public function like($values)
    {
        $this->_add("LIKE", $values);
    }

    public function greaterThan($values)
    {
        $this->_add("GREATER_THAN", $values);
    }

    public function lessThan($values)
    {
        $this->_add("LESS_THAN", $values);
    }

    public function greaterThanEqual($values)
    {
        $this->_add("GREATER_THAN_EQUAL", $values);
    }

    public function lessThanEqual($values)
    {
        $this->_add("LESS_THAN_EQUAL", $values);
    }

    public function between($values)
    {
        $this->_add("BETWEEN", $values);
    }

    public function beginsWith($values)
    {
        $this->_add("BEGINS_WITH", $values);
    }

    public function endsWith($values)
    {
        $this->_add("ENDS_WITH", $values);
    }

    public function contains($values)
    {
        $this->_add("CONTAINS", $values);
    }

    public function notEquals($values)
    {
        $this->_add("EQUALS", $values, true);
    }

    public function notLike($values)
    {
        $this->_add("LIKE", $values, true);
    }

    public function notGreaterThan($values)
    {
        $this->_add("GREATER_THAN", $values, true);
    }

    public function notLessThan($values)
    {
        $this->_add("LESS_THAN", $values, true);
    }

    public function notGreaterThanEqual($values)
    {
        $this->_add("GREATER_THAN_EQUAL", $values, true);
    }

    public function notLessThanEqual($values)
    {
        $this->_add("LESS_THAN_EQUAL", $values, true);
    }

    public function notBetween($values)
    {
        $this->_add("BETWEEN", $values, true);
    }

    public function notBeginsWith($values)
    {
        $this->_add("BEGINS_WITH", $values, true);
    }

    public function notEndsWith($values)
    {
        $this->_add("ENDS_WITH", $values, true);
    }

    public function notContains($values)
    {
        $this->_add("CONTAINS", $values, true);
    }

}