<?php

namespace WoganMay\DomoPHP\Service;

use GuzzleHttp\Exception\ClientException;
use WoganMay\DomoPHP\Client;
use WoganMay\DomoPHP\DomoPHPException;
use WoganMay\DomoPHP\Util;

class DataSet
{
    public function __construct(private Client $client) { }

    public function list(int $limit = 50, int $offset = 0, ?string $sort = null, ?string $nameLike = null) : array
    {
        return $this->client->connector()->getJSON("/v1/datasets", Util::trimArrayKeys([
            'limit' => $limit,
            'offset' => $offset,
            'sort' => $sort,
            'nameLike' => $nameLike
        ]));
    }

    public function create(string $name, array $schema, ?string $description = null) : mixed
    {

        // The $schema should be an array of columns, with a name and type. The domo-php library expects
        // a simple key/value set, and unpacks it into the format that the API expects here:

        $cleanSchema = ['columns' => []];
        foreach($schema as $fieldName => $fieldType) $cleanSchema['columns'][] = [ 'type' => $fieldType, 'name' => $fieldName];

        $params = [
            'name' => $name,
            'description' => $description ?? "Created by domo-php at " . date("Y-m-d H:i:s"),
            'schema' => $cleanSchema
        ];

        return $this->client->connector()->postJSON("/v1/datasets", $params);

    }

    public function get(string $id) : object
    {
        try {
            return $this->client->connector()->getJSON("/v1/datasets/{$id}");
        }
        catch(ClientException $clientException)
        {
            // This $clientException could be a 404, or could be some other issue that
            // is preventing us from fetching a dataset. That said, if the dataset we're
            // trying to work with does not exist, the program on our side should terminate.
            throw new DomoPHPException("DataSet@get", $clientException);
        }
    }

    public function update(string $id, string $name, ?string $description = null, ?bool $pdpEnabled = null) : mixed
    {
        // Assemble the update we want to send to the server. By default, you'll update() a dataset to rename it.
        $params = [
            'name' => $name
        ];

        if ($description !== null) $params['description'] = $description;
        if ($pdpEnabled !== null) $params['pdpEnabled'] = $pdpEnabled;

        return $this->client->connector()->putJSON("/v1/datasets/{$id}", Util::trimArrayKeys($params));
    }

    public function delete(string $id) : bool
    {
        return $this->client->connector()->delete("/v1/datasets/{$id}");
    }

    public function query(string $id, string $sql)
    {
        try {
            return $this->client->connector()->postJSON("/v1/datasets/query/execute/{$id}", [ 'sql' => $sql ]);
        }
        catch(ClientException $clientException)
        {
            // Querying a dataset with no rows in it produces a `400 Bad Request` response.
            throw new DomoPHPException("DataSet@query", $clientException);
        }
    }

    public function import(string $id, string $csv) : mixed
    {
        return $this->client->connector()->putCSV("/v1/datasets/{$id}/data", $csv);
    }

    public function export(string $id, bool $includeHeader = true, string $fileName = "export.csv") : string
    {
        // As of 9 July 2023, this method doesn't actually work on Domo's API. The below code is how it would normally
        // be called, but until the upstream issue is resolved, this method will only return a blank string.
        // Q&A URL: https://community-forums.domo.com/main/discussion/59853/does-the-apis-dataset-export-method-work/p1
        throw new DomoPHPException("DataSet@export");

        // Actual implementation:
        // return $this->client->connector()->getCSV("/v1/datasets/{$id}/data", Util::trimArrayKeys([
        //     'includeHeader' => $includeHeader,
        //     'fileName' => $fileName
        // ]));
    }

    public function getPDP()
    {
        throw new \Exception("Not implemented yet");
    }

    public function updatePDP()
    {
        throw new \Exception("Not implemented yet");
    }

    public function deletePDP()
    {
        throw new \Exception("Not implemented yet");
    }

    public function listPDP()
    {
        throw new \Exception("Not implemented yet");
    }

    public function createPDP()
    {
        throw new \Exception("Not implemented yet");
    }

}