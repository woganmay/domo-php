<?php

namespace WoganMay\DomoPHP;

class DataSet
{
    var $DomoAPIClient = null;
    
    public function __construct($APIClient)
    {
        $this->DomoAPIClient = $APIClient;
    }
    
    public function create($name, $description, $columns)
    {
        // Try creating a set
        $body = [
            "name" => $name,
            "description" => $description,
            "schema" => [
                "columns" => $columns
            ]
        ];
        
        return $this->DomoAPIClient->post("/v1/datasets", $body);
    }
    
    public function getMetaData($id = null)
    {
        if ($id == null)
            throw new \Exception("ID cannot be null!");
            
        return $this->DomoAPIClient->get("/v1/datasets/$id?fields=all");
    }
    
    public function getList($limit = 10, $offset = 20, $sort = "name", $fields = "all")
    {
        $url = sprintf("/v1/datasets?sort=%s&fields=%s&offset=%s&limit=%s", $sort, $fields, $offset, $limit);
        return $this->DomoAPIClient->get($url);
    }
    
    public function update($id, $update)
    {
        return $this->DomoAPIClient->put("/v1/datasets/$id", $update);
    }
    
    public function delete($id)
    {
        return $this->DomoAPIClient->delete("/v1/datasets/$id");
    }
    
    public function export($id)
    {
        $response = $this->DomoAPIClient->WebClient->get("/v1/datasets/$id/data", [
            'headers' => [
                'Authorization' => 'Bearer '.$this->DomoAPIClient->getToken(),
                'Accept' => 'text/csv'
            ]
        ]);
        
        // Handle server response
        switch($response->getStatusCode())
        {
            case 200:
                // CSV Exported
                return (string)$response->getBody();
            default:
                // Unknown result code!
                throw new \Exception($response->getBody());
        }
        
    }
    
    public function import($id, $csv)
    {
        
        $response = $this->DomoAPIClient->WebClient->put("/v1/datasets/$id/data", [
            'headers' => [
                'Authorization' => 'Bearer '.$this->DomoAPIClient->getToken(),
                'Content-Type' => 'text/csv'
            ],
            'body' => $csv
        ]);

        // Handle server response
        switch($response->getStatusCode())
        {
            case 200:
                // Resource Updated
                return json_decode($response->getBody());
            default:
                // Unknown result code!
                throw new \Exception($response->getBody());
        }
    }
    
}
    