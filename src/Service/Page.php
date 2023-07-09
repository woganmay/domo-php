<?php

namespace WoganMay\DomoPHP\Service;

use WoganMay\DomoPHP\Client;

class Page
{
    public function __construct(private Client $client) { }

    public function placeholderMethod() : bool
    {
        return true;
    }
}