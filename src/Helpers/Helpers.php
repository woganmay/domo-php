<?php

namespace WoganMay\DomoPHP\Helpers;

/**
 * Class Helpers
 *
 * Sets up the helper instances
 *
 * @package WoganMay\DomoPHP\Helpers
 */
class Helpers
{
    /** @var DataSet */
    public $DataSet;

    /** @var SchemaBuilder */
    public $SchemaBuilder;

    /** @var FilterBuilder */
    public $FilterBuilder;

    public function __construct()
    {
        $this->DataSet = new DataSet;
        $this->SchemaBuilder = new SchemaBuilder;
        $this->FilterBuilder = new FilterBuilder;
    }

}