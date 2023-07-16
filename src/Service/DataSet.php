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

    public function export(string $id, bool $includeHeader = true) : string
    {
        return $this->client->connector()->getCSV("/v1/datasets/{$id}/data", Util::trimArrayKeys([
            'includeHeader' => $includeHeader,
            'fileName' => "export.csv"
        ]));
    }

    public function listPDP(string $id) : array
    {
        try {
            return $this->client->connector()->getJSON("/v1/datasets/{$id}/policies");
        }
        catch(ClientException $clientException)
        {
            throw new DomoPHPException("DataSet@listPDP", $clientException);
        }
    }

    /**
     * @param string $id The ID of the DataSet to create the PDP on
     * @param string $name The user-friendly name of the PDP
     * @param array $filters An array of filter criteria to apply to the PDP
     * @param array $users An array of user IDs (integer) to link to the PDP
     * @param array $groups An array of group IDs (integer) to link to the PDP
     * @param string $type The type of PDP, defaults to "user"
     * @return mixed The created PDP
     * @throws DomoPHPException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createPDP(string $id, string $name, array $filters = [], array $users = [], array $groups = [], string $type = "user") : mixed
    {
        try {
            return $this->client->connector()->postJSON("/v1/datasets/{$id}/policies", [
                'name' => $name,
                'filters' => $filters,
                'users' => $users,
                'groups' => $groups,
                'type' => $type
            ]);
        }
        catch(ClientException $clientException)
        {
            throw new DomoPHPException("DataSet@createPDP", $clientException);
        }
    }

    public function getPDP(string $id, int $pdp_id) : mixed
    {
        try {
            return $this->client->connector()->getJSON("/v1/datasets/{$id}/policies/{$pdp_id}");
        }
        catch(ClientException $clientException)
        {
            throw new DomoPHPException("DataSet@getPDP", $clientException);
        }
    }

    public function updatePDP(string $id, int $pdp_id, array $updates = []) : mixed
    {
        try {
            return $this->client->connector()->putJSON("/v1/datasets/{$id}/policies/{$pdp_id}", $updates);
        }
        catch(ClientException $clientException)
        {
            throw new DomoPHPException("DataSet@updatePDP", $clientException);
        }
    }

    public function deletePDP(string $id, int $pdp_id) : bool
    {
        try {
            return $this->client->connector()->delete("/v1/datasets/{$id}/policies/{$pdp_id}");
        }
        catch(ClientException $clientException)
        {
            throw new DomoPHPException("DataSet@deletePDP", $clientException);
        }
    }

}