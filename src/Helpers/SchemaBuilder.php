<?php

namespace WoganMay\DomoPHP\Helpers;

class SchemaBuilder
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

    /**
     * @param $headers The array of headers from the CSV file
     * @param $record The first record of data
     * @return array A schema ready for import
     */
    public function inferSchema($headers, $record)
    {
        $columns = [];

        foreach ($record as $n => $v) {
            if (is_float($v)) {
                $type = 'DECIMAL';
            } elseif (is_float($v)) {
                $type = 'DOUBLE';
            } elseif (is_numeric($v)) {
                $type = 'LONG';
            } elseif (strtotime($v)) {
                // It validates as a datetime

                if (strlen($v) <= 10) {
                    // Too short to include a time
                    $type = 'DATE';
                } else {
                    $type = 'DATETIME';
                }
            } else {
                $type = 'STRING';
            }

            $columns[] = [
                'type' => $type,
                'name' => $headers[$n],
            ];
        }

        return $columns;
    }

}