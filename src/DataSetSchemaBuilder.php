<?php

namespace WoganMay\DomoPHP;

class DataSetSchemaBuilder
{
    private $columns;

    public function __construct()
    {
        $this->columns = [];
    }

    /**
     * @param $name Name of the STRING column
     */
    public function string($name) {
        $this->columns[] = [ "name" => $name, "type" => "STRING" ];
    }

    /**
     * @param $name Name of the DECIMAL column
     */
    public function decimal($name) {
        $this->columns[] = [ "name" => $name, "type" => "DECIMAL" ];
    }

    /**
     * @param $name Name of the LONG column
     */
    public function long($name) {
        $this->columns[] = [ "name" => $name, "type" => "LONG" ];
    }

    /**
     * @param $name Name of the DOUBLE column
     */
    public function double($name) {
        $this->columns[] = [ "name" => $name, "type" => "DOUBLE" ];
    }

    /**
     * @param $name Name of the DATE column
     */
    public function date($name) {
        $this->columns[] = [ "name" => $name, "type" => "DATE" ];
    }

    /**
     * @param $name Name of the DATETIME column
     */
    public function datetime($name) {
        $this->columns[] = [ "name" => $name, "type" => "DATETIME" ];
    }

    /**
     * @return array Return the schema array
     */
    public function toArray() {
        return $this->columns;
    }

}