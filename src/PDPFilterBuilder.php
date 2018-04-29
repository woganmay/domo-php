<?php

namespace WoganMay\DomoPHP;

class PDPFilterBuilder
{
    private $filters;

    public function __construct()
    {
        $this->filters = [];
    }

    public function toArray()
    {
        // Publish the array
        return [];
    }

    /**
     * @param $camelCase CamelCase string
     * @return string Underscored string
     */
    private function decamelize($camelCase)
    {
        return strtoupper(
            preg_replace(
                ["/([A-Z]+)/", "/_([A-Z]+)([A-Z][a-z])/"],
                ["_$1", "_$1_$2"],
                lcfirst($camelCase)
            )
        );
    }

    public function __call($name, $arguments)
    {

        $not = substr($name, 0, 3) == "not";

        if ($not)
        {
            $operator = substr($name, 3);
            $operator[0] = strtolower($operator[0]);
        }
        else
        {
            $operator = $name;
        }

        $valid_operators = ["equals", "like", "greaterThan", "lessThan",
                            "greaterThanEqual", "lessThanEqual", "between",
                            "beginsWith", "endsWith", "contains"];

        if (in_array($operator, $valid_operators, false))
        {
            // Stack the operator
            $this->filters[] = [
                "operator" => $this->decamelize($operator),
                "column" => $arguments[0],
                "values" => $arguments[1],
                "not" => $not
            ];
        }

    }

    /**
     * @return array The compiled filters
     */
    public function render()
    {
        return $this->filters;
    }


}