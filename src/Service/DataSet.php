<?php

namespace WoganMay\DomoPHP\Service;

use WoganMay\DomoPHP\Client;

class DataSet
{
    public function __construct(private Client $client) { }

    public function placeholderMethod() : bool
    {
        return true;
    }
}