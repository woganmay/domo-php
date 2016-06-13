<?php

namespace WoganMay\DomoPHP\Tests;

use WoganMay\DomoPHP\DomoAPIClient;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    
    protected static $dataSet;

    public function setUp()
    {
        $conf_client_id = "See developer.domo.com";
        $conf_client_secret = "See developer.domo.com";
    
        $this->client = new DomoAPIClient($conf_client_id, $conf_client_secret);
        
        $this->testCSVData = "R1C1,R1C2\nR2C1,R2C2";
    }
    
    public function testLogin()
    {
        $this->client->getToken();
    }
    
    public function testCreate()
    {
        $name = "My Test Dataset";
        $desc = "My test description";
        
        $columns = [
            [
                "type" => "STRING",
                "name" => "Column 1"
            ],
            [
                "type" => "STRING",
                "name" => "Column 2"
            ]
        ];
        
        $dataSet = $this->client->DataSet->create($name, $desc, $columns);
        
        $this->assertEquals($name, $dataSet->name);
        $this->assertEquals($desc, $dataSet->description);
        
        // Get the metadata for this set
        self::$dataSet = $dataSet;
        
    }
    
    /**
     * @depends testCreate
     */
    public function testGetMetadata()
    {
        $metadata = $this->client->DataSet->getMetadata(self::$dataSet->id);
        
        $this->assertEquals(self::$dataSet->name, $metadata->name);
        $this->assertEquals(self::$dataSet->description, $metadata->description);
    }
    
    public function testGetList()
    {
        // Will throw an exception for a non-20x response code
        $list = $this->client->DataSet->getList();
    }
    
    public function testUpdate()
    {
        
        $this->markTestSkipped('Does not appear that the Update methods actually do anything');
        
        // Update an existing dataset
        $update = [
            "name" => "A better name"
        ];
        
        $updated = $this->client->DataSet->update(self::$dataSet->id, $update);

        $this->assertEquals(self::$dataSet->description, $updated->description);
        //$this->assertEquals($update["name"], $updated->name);
        
        // Update two
        $update = [
            "description" => "A better description"
        ];
        
        $updated = $this->client->DataSet->update(self::$dataSet->id, $update);

        $this->assertEquals(self::$dataSet->name, $updated->name);
        //$this->assertEquals($update["description"], $updated->description);
        
        // Update three
        $update = [
            "description" => "The final description",
            "name" => "The final name"
        ];
        
        $updated = $this->client->DataSet->update(self::$dataSet->id, $update);
        
        //$this->assertEquals($update["name"], $updated->name);
        //$this->assertEquals($update["description"], $updated->description);
    }
    
    public function testDelete()
    {
        $this->markTestSkipped('Does not appear that the Delete methods actually do anything');
        
        // Create a DataSet
        $dataSet = $this->client->DataSet->create("TMPDEL", "", [['type'=>'STRING','name'=>'tmpcol']]);
        
        // Delete dataset
        $result = $this->client->DataSet->delete($dataSet->id);
        
        $this->assertTrue($result);
        
    }
    
    public function testImport()
    {
        $result = $this->client->DataSet->import(self::$dataSet->id, $this->testCSVData);
        
        // should exception if there's a problem
        
    }
    
    /**
     * @depends testImport
     */
    public function testExport()
    {
        $csv = $this->client->DataSet->export(self::$dataSet->id);
        
        // should exception if there's a problem
    }
    
}