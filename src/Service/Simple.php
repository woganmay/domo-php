<?php

namespace WoganMay\DomoPHP\Service;

use WoganMay\DomoPHP\Client;
use WoganMay\DomoPHP\Util;

class Simple
{
    public function __construct(private Client $client) { }

    public function create(string $name, ?string $description = "Created by domo-php") : \stdClass
    {
        return $this->client->connector()->postJSON("/v1/json", Util::trimArrayKeys([
            'name' => $name,
            'description' => $description
        ]));
    }

    public function populate(string $id, array $data) : mixed
    {
        return $this->client->connector()->putJSON("/v1/json/{$id}/data", $data);
    }
}