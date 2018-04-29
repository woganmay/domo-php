<?php

namespace WoganMay\DomoPHP\Services;

/**
 * DomoPHP DataSet.
 *
 * Utility methods for working with datasets
 *
 * @author     Wogan May <wogan.may@gmail.com>
 * @license    MIT
 * @link       https://github.com/woganmay/domo-php
 */
class DataSet
{
    private $Client = null;

    /**
     * oAuth Client ID.
     *
     * The Client ID obtained from developer.domo.com
     *
     * @param \WoganMay\DomoPHP\Client $APIClient An instance of the API Client
     */
    public function __construct(\WoganMay\DomoPHP\Client $APIClient)
    {
        $this->Client = $APIClient;
    }

    /**
     * Create a new DataSet.
     *
     * @param string $name The name of the dataset
     * @param array $columns A list of columns to crate
     * @param string $description
     * @return json
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createDataSet($name, $columns, $description = '')
    {
        // Format for creating a dataset
        $body = [
            'name'        => $name,
            'description' => $description,
            'schema'      => [
                'columns' => $columns,
            ],
        ];

        return $this->Client->postJSON('/v1/datasets', $body);
    }

    /**
     * Get DataSet Metadata.
     *
     * @param string $id The GUID to get metadata for
     * @return json
     * @throws \Exception
     */
    public function getDataSet($id = null)
    {
        if ($id == null) {
            throw new \Exception('ID cannot be null!');
        }

        return $this->client->getJSON("/v1/datasets/$id?fields=all");
    }

    /**
     * Get a List of DataSets.
     *
     * @param int    $limit  (Default 10) The number of datasets to return
     * @param int    $offset  (Default 0) Used for pagination
     * @param string $sort   (Default 'name') The field to sort by
     * @param string $fields (Default 'all') The fields to return in the result
     * @return json
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
     * @return json
     */
    public function updateDataSet($id, $update)
    {
        return $this->Client->put("/v1/datasets/$id", $update);
    }

    /**
     * Delete DataSet.
     *
     * @param string $id The GUID to delete
     * @return boolean
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteDataSet($id)
    {
        $response = $this->Client->WebClient->delete("/v1/datasets/$id", [
            'headers' => [
                'Authorization' => 'Bearer '.$this->Client->getToken(),
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
     * @param bool $csvHeaders Include CSV headers
     *
     * @return string The CSV content
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function exportDataSet($id, $csvHeaders = null)
    {

        $csvHeaders = ($csvHeaders == null) ? false : true;

        $response = $this->Client->WebClient->get("/v1/datasets/$id/data?includeHeader=$csvHeaders", [
            'headers' => [
                'Authorization' => 'Bearer '.$this->Client->getToken(),
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
     * @param string $id The GUID to import to
     * @param string $csv CSV data to upload
     * @return mixed
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function importDataSet($id, $csv)
    {
        $response = $this->Client->WebClient->put("/v1/datasets/$id/data", [
            'headers' => [
                'Authorization' => 'Bearer '.$this->Client->getToken(),
                'Content-Type'  => 'text/csv',
            ],
            'body' => $csv,
        ]);

        // Handle server response
        switch ($response->getStatusCode()) {
            case 200:
            case 204:
                // Resource Updated
                return json_decode($response->getBody());
            default:
                // Unknown result code!
                throw new \Exception($response->getBody());
        }
    }

    /**
     * @param $id string The DataSet GUID
     * @param $pdp string The PDP GUID
     * @return mixed
     */
    public function getDataSetPDP($id, $pdp)
    {
        $url = sprintf('/v1/datasets/%s/policies/%s', $id, $pdp);

        return $this->getJSON($url);
    }

    /**
     * @param $id string The DataSet GUID to apply the PDP tp
     * @param $name string The name of the PDP
     * @param $type string The Type (user or system)
     * @param $users array The list of User IDs to apply the policy to
     * @param $groups array The list of Group IDs to apply the policy to
     * @param $filters array The filters to apply in the policy
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createDataSetPDP($id, $name, $filters, $type = "user", $users = [], $groups = [])
    {
        $body = [
            'name'    => $name,
            'type'    => $type,
            'filters' => $filters
        ];

        if (!empty($users)) $body['users'] = $users;
        if (!empty($groups)) $body['groups'] = $groups;

        return $this->Client->postJSON('/v1/datasets/' . $id, $body);
    }

    /**
     * @param $id The DataSet GUID
     * @param $pdp The PDP ID
     * @param $update The array of updates to apply
     * @return mixed The results of the update request
     */
    public function updateDataSetPDP($id, $pdp, $update)
    {
        return $this->Client->put("/v1/datasets/$id/policies/$pdp", $update);
    }

    /**
     * @param $id The GUID of the DataSet
     * @param $pdp The ID of the PDP to remove
     * @return bool Whether the PDP was deleted successfully
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteDataSetPDP($id, $pdp)
    {
        $response = $this->Client->WebClient->delete("/v1/datasets/$id/policies/$pdp", [
            'headers' => [
                'Authorization' => 'Bearer '.$this->Client->getToken(),
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
     * @param $id The DataSet GUID
     * @return mixed The list of PDP policies on the dataset
     */
    public function getPDPList($id)
    {
        $url = sprintf('/v1/datasets/%s/policies', $id);

        return $this->getJSON($url);
    }

}
