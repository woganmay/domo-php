<?php

namespace WoganMay\DomoPHP;

/**
 * DomoPHP DataSet.
 *
 * Utility methods for working with datasets
 *
 * @author     Wogan May <wogan.may@gmail.com>
 * @license    MIT
 *
 * @link       https://github.com/woganmay/domo-php
 */
class DataSet
{
    public $DomoAPIClient = null;

    /**
     * oAuth Client ID.
     *
     * The Client ID obtained from developer.domo.com
     *
     * @param object $APIClient An instance of the API Client
     */
    public function __construct($APIClient)
    {
        $this->DomoAPIClient = $APIClient;
    }

    /**
     * Post JSON to the API.
     *
     * Send an array as JSON, and read the response
     *
     * @param string $url  The relative URL to post to
     * @param array  $body The body array to send
     */
    private function postJSON($url, $body)
    {
        $response = $this->DomoAPIClient->WebClient->post($url, [
            'headers' => [
                'Authorization' => 'Bearer '.$this->DomoAPIClient->getToken(),
            ],
            'json' => $body,
        ]);

        // Handle server response
        switch ($response->getStatusCode()) {
            case 200: // Request successful
            case 201: // New resource created, OK

                return json_decode($response->getBody());

            default:

                // Unknown result code!
                throw new \Exception($response->getBody());
        }
    }

    /**
     * Post JSON to the API.
     *
     * Send an array as JSON, and read the response
     *
     * @param string $url The relative URL to get
     */
    private function getJSON($url)
    {
        $response = $this->DomoAPIClient->WebClient->get($url, [
            'headers' => [
                'Authorization' => 'Bearer '.$this->DomoAPIClient->getToken(),
            ],
        ]);

        // Handle server response
        switch ($response->getStatusCode()) {
            case 200:
                // Got the resource
                return json_decode($response->getBody());
            default:
                // Unknown result code!
                throw new \Exception($response->getBody());
        }
    }

    /**
     * Create a new DataSet.
     *
     * @param string  $name        The name of the dataset
     * @param array   $columns     A list of columns to crate
     * @param string? $description An optional description
     */
    public function create($name, $columns, $description = '')
    {
        // Format for creating a dataset
        $body = [
            'name'        => $name,
            'description' => $description,
            'schema'      => [
                'columns' => $columns,
            ],
        ];

        return $this->postJSON('/v1/datasets', $body);
    }

    /**
     * Get DataSet Metadata.
     *
     * @param string $id The GUID to get metadata for
     */
    public function getMetaData($id = null)
    {
        if ($id == null) {
            throw new \Exception('ID cannot be null!');
        }

        return $this->getJSON("/v1/datasets/$id?fields=all");
    }

    /**
     * Get a List of DataSets.
     *
     * @param int    $limit  (Default 10) The number of datasets to return
     * @param int    $limit  (Default 0) Used for pagination
     * @param string $sort   (Default 'name') The field to sort by
     * @param string $fields (Default 'all') The fields to return in the result
     */
    public function getList($limit = 10, $offset = 0, $sort = 'name', $fields = 'all')
    {
        $url = sprintf('/v1/datasets?sort=%s&fields=%s&offset=%s&limit=%s', $sort, $fields, $offset, $limit);

        return $this->getJSON($url);
    }

    /**
     * Update DataSet Metadata.
     *
     * @param string $id     The GUID to update
     * @param array  $update The object to overwrite with
     */
    public function update($id, $update)
    {
        return $this->DomoAPIClient->put("/v1/datasets/$id", $update);
    }

    /**
     * Delete DataSet.
     *
     * @param string $id The GUID to delete
     */
    public function delete($id)
    {
        $response = $this->DomoAPIClient->WebClient->delete("/v1/datasets/$id", [
            'headers' => [
                'Authorization' => 'Bearer '.$this->getToken(),
            ],
        ]);

        // Handle server response
        switch ($response->getStatusCode()) {
            case 204:
                // Deleted OK
                return true;
            default:
                // Unknown result code!
                throw new \Exception($response->getBody());
        }
    }

    /**
     * Export DataSet to CSV.
     *
     * @param string $id The GUID to export
     *
     * @return The CSV
     */
    public function export($id)
    {
        $response = $this->DomoAPIClient->WebClient->get("/v1/datasets/$id/data", [
            'headers' => [
                'Authorization' => 'Bearer '.$this->DomoAPIClient->getToken(),
                'Accept'        => 'text/csv',
            ],
        ]);

        // Handle server response
        switch ($response->getStatusCode()) {
            case 200:
                // CSV Exported
                return (string) $response->getBody();
            default:
                // Unknown result code!
                throw new \Exception($response->getBody());
        }
    }

    /**
     * Import CSV to DataSet.
     *
     * @param string $id  The GUID to import to
     * @param string $csv CSV data to upload
     */
    public function import($id, $csv)
    {
        $response = $this->DomoAPIClient->WebClient->put("/v1/datasets/$id/data", [
            'headers' => [
                'Authorization' => 'Bearer '.$this->DomoAPIClient->getToken(),
                'Content-Type'  => 'text/csv',
            ],
            'body' => $csv,
        ]);

        // Handle server response
        switch ($response->getStatusCode()) {
            case 200:
                // Resource Updated
                return json_decode($response->getBody());
            default:
                // Unknown result code!
                throw new \Exception($response->getBody());
        }
    }
}
