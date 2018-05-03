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
     * @param string $guid The GUID to get metadata for
     * @return mixed
     * @throws \Exception
     */
    public function getDataSet($guid)
    {
        return $this->Client->getJSON("/v1/datasets/$guid?fields=all");
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

        return $this->Client->getJSON($url);
    }

    /**
     * Update DataSet Metadata.
     *
     * @param string $id     The GUID to update
     * @param array  $update An array of fields to change (name, description, schema)
     * @return json
     */
    public function updateDataSet($id, $update)
    {
        return $this->Client->putJSON("/v1/datasets/$id", $update);
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
     * @param bool $header TRUE to include CSV headers (default)
     *
     * @return string The CSV content
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function exportDataSet($id, $header = true)
    {
        $url = "/v1/datasets/$id/data" . (($header) ? "?includeHeader=true" : "");

        $response = $this->Client->WebClient->get($url, [
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

        return $response->getStatusCode() == 204;

    }

    /**
     * @param $id string The DataSet GUID
     * @param $pdp string The PDP GUID
     * @return mixed
     */
    public function getDataSetPDP($id, $pdp)
    {
        $url = sprintf('/v1/datasets/%s/policies/%s', $id, $pdp);

        return $this->Client->getJSON($url);
    }

    /**
     * @param $id string The DataSet GUID to apply the PDP tp
     * @param $name string The name of the PDP
     * @param $type string The Type (open or user)
     * @param $users array The list of User IDs to apply the policy to
     * @param $groups array The list of Group IDs to apply the policy to
     * @param $filters array The filters to apply in the policy
     * @return string
     * @throws \Exception
     */
    public function createDataSetPDP($id, $name, $filters, $type = "open", $users = [], $groups = [])
    {
        $body = [
            'name'    => $name,
            'type'    => $type,
            'filters' => $filters
        ];

        if (!empty($users) && !empty($groups)) throw new \Exception("Needs either \$users or \$groups");

        if (!empty($users)) $body['users'] = $users;
        if (!empty($groups)) $body['groups'] = $groups;

        try
        {
            return $this->Client->postJSON('/v1/datasets/' . $id . '/policies', $body);
        }
        catch(\GuzzleHttp\Exception\ClientException $ex)
        {
            throw new \Exception($ex->getMessage());
        }

    }

    /**
     * @param string $id The DataSet GUID
     * @param integer $pdp The PDP ID
     * @param string $name The name of the PDP
     * @param array $filters The array of filters to apply
     * @param array $users The list of users to apply to
     * @param array $groups The groups this applies to
     * @return mixed The results of the update request
     * @throws \Exception
     */
    public function updateDataSetPDP($id, $pdp, $name, $filters, $users = [], $groups = [])
    {
        $update = [
            'name' => $name,
            'filters' => $filters
        ];

        if (!empty($users)) $update['users'] = $users;
        if (!empty($groups)) $update['groups'] = $groups;

        return $this->Client->putJSON("/v1/datasets/$id/policies/$pdp", $update);
    }

    /**
     * @param $id The GUID of the DataSet
     * @param $pdp The ID of the PDP to remove
     * @return bool Whether the PDP was deleted successfully
     * @throws \Exception
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

        return $this->Client->getJSON($url);
    }

}
