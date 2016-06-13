<?php

namespace WoganMay\DomoPHP;

/**
 * DomoPHP Client
 *
 * The DomoPHP client implements a simple object-based way to access the Domo
 * API.
 * 
 * Currently the client is focused on the Data methods.
 * 
 * @package    DomoPHP
 * @author     Wogan May <wogan.may@gmail.com>
 * @license    MIT
 * @link       https://github.com/woganmay/domo-php
 */

class DomoPHP {

    public $API = null;

    public function __construct($client_id, $client_secret)
    {
        $this->API = new DomoAPIClient($client_id, $client_secret);
    }
    
    /**
     * Create a new dataset based on a CSV file
     *
     * @param string $name DataSet Name
     * @param resource $handle An open file handle to the CSV file
     */
    public function createDataSet($name, $file)
    {
        if (!file_exists($file))
            throw new \Exception("File not found!");
        
        $handle = fopen($file, "r");
        
        $headers = [];
        $columns = [];
        $csv = "";
        
        if ($handle) {
            
            $pos = 0;
            
            while (($data = fgetcsv($handle)) !== false) {
                
                // Process $line
                
                if ($pos == 0)
                {
                    $headers = $data;
                }
                else
                {
                    if ($pos == 1)
                        $columns = $this->inferSchema($headers, $data);
                    
                    // Process the rest of the CSV
                    foreach($data as $k => $v) $data[$k] = str_replace("'", "\'", $v);
                    $csv .= "'" . implode("','", $data) . "'\n";
                }
             
                $pos++;
                
            }
            
            fclose($handle);
            
            // Create the dataset
            $dataSet = $this->API->DataSet->create($name, "", $columns);
            
            // Update it
            $populate = $this->API->DataSet->import($id, $csv);
            
            return $dataSet;
            
        }
        else
        {
            throw new \Exception("Error reading from the file");
        }
    }
    
    public function inferSchema($headers, $record)
    {
        $columns = [];
        
        foreach($record as $n => $v)
        {
            
            if (is_float($v))
            {
                $type = "DECIMAL";
            }
            elseif(is_double($v))
            {
                $type = "DOUBLE";
            }
            elseif(is_numeric($v))
            {
                $type = "LONG";
            }
            elseif(strtotime($v))
            {
                // It validates as a datetime
                
                if (strlen($v) <= 10)
                {
                    // Too short to include a time
                    $type = "DATE";
                }
                else
                {
                    $type = "DATETIME";
                }
                
            }
            else
            {
                $type = "STRING";
            }
            
            $columns[] = [
                "type" => $type,
                "name" => $headers[$n]
            ];
            
        }
        
        return $columns;
    }
    
}