<?php

use PHPUnit\Framework\TestCase;
use WoganMay\DomoPHP\Client;
use WoganMay\DomoPHP\DomoPHPException;

final class DataSetServiceTest extends TestCase
{
    public function testListDatasets()
    {
        $client = new Client();

        $datasets = $client->dataSet()->list();

        $this->assertIsArray($datasets);

        // Assumes there is at least 1 dataset in Domo
        $this->assertGreaterThan(0, count($datasets));

    }

    public function testCreateDataset()
    {
        $client = new Client();

        $name = "Test DataSet " . uniqid();

        $description = "Created by domo-php's test runner";

        $schema = [
            'Name' => 'STRING',
            'Price' => 'DECIMAL',
            'Sales' => 'LONG',
            'Tax' => 'DOUBLE',
            'Recognize Date' => 'DATE',
            'Timestamp' => 'DATETIME'
        ];

        $dataset = $client->dataSet()->create($name, $schema, $description);

        $this->assertEquals($name, $dataset->name);
        $this->assertEquals(0, $dataset->rows);

    }

    public function testGetDataset()
    {
        $client = new Client();

        $hash = uniqid();

        // Create a new dataset, so we have an ID to fetch
        $dataset = $client->dataSet()->create("Test GetDataset {$hash}", [ 'Field' => 'STRING' ]);

        // Now pull that from scratch
        $retrievedDataset = $client->dataSet()->get($dataset->id);

        $this->assertEquals($dataset->name, $retrievedDataset->name);
    }

    public function testUpdateDataset()
    {
        $client = new Client();

        $hash = uniqid();

        // Create a new dataset, so we have a target for updating
        $dataset = $client->dataSet()->create("Test UpdateDataset {$hash}", [ 'Field' => 'STRING' ]);

        // Update the name and description to something random and verify it's being returned by the API
        $newName = "Test UpdateDataset1 {$hash}";
        $newDescription = "Updated description for Test UpdateDataset {$hash}";

        // Do the update
        $client->dataSet()->update($dataset->id, $newName, $newDescription);

        // Fetch the dataset from scratch to validate
        $retrievedDataset = $client->dataSet()->get($dataset->id);

        $this->assertEquals($newName, $retrievedDataset->name);
        $this->assertEquals($newDescription, $retrievedDataset->description);


    }

    public function testDeleteDataset()
    {
        $client = new Client();

        $hash = uniqid();

        // Create a new dataset, so we have a target for deleting
        $dataset = $client->dataSet()->create("Test DeleteDataset {$hash}", [ 'Field' => 'STRING' ]);

        $id = $dataset->id;

        // Attempt deletion
        $deleteResult = $client->dataSet()->delete($id);

        $this->assertTrue($deleteResult);

        // Verify we can't fetch it anymore
        $this->expectException(DomoPHPException::class);
        $client->dataSet()->get($id);

    }

    public function testImportDataset()
    {
        $client = new Client();

        $hash = uniqid();

        // Create a new dataset, so we have a target for importing
        $dataset = $client->dataSet()->create("Test DeleteDataset {$hash}", [ 'Field' => 'STRING' ]);

        // Load some CSV data - 3 rows
        $csv = "Field\nRow 1\nRow 2\nRow 3";

        $importResult = $client->dataSet()->import($dataset->id, $csv);

        $this->assertTrue($importResult);

    }

    public function testQueryDataset()
    {
        $client = new Client();

        $hash = uniqid();

        // Note: There is a delay between importing data, and the data being available to query. If you
        // try running a query/execute during that window, you get a `400 Bad Request` response. So, in order
        // to test this properly, this test deliberately waits for _10_ seconds before attempting the query.

        // Create a new dataset, so we have a target for importing
        $dataset = $client->dataSet()->create("Test QueryDataset {$hash}", [ 'Field' => 'STRING' ]);
        $client->dataSet()->import($dataset->id, "Field\nRow 1\nRow 2\nRow 3");

        // Give Domo a chance to run whatever internal processes are required
        sleep(10);

        // We should now be able to query this
        $queryResult = $client->dataSet()->query($dataset->id, "SELECT * FROM table");

        $this->assertEquals($dataset->id, $queryResult->datasource);
        $this->assertCount(1, $queryResult->columns);
        $this->assertCount(3, $queryResult->rows);

    }

    public function testExportDataset()
    {
        // As of 9 July 2023, this method returns a `406 Not Acceptable` response regardless of how it is queried.
        // Q&A URL: https://community-forums.domo.com/main/discussion/59853/does-the-apis-dataset-export-method-work/p1
        $this->markTestSkipped("API method not supported by Domo.com");
    }

    public function testGetDatasetPDP()
    {
        $this->markTestSkipped("Test not implemented yet");
    }

    public function testUpdateDatasetPDP()
    {
        $this->markTestSkipped("Test not implemented yet");
    }

    public function testDeleteDatasetPDP()
    {
        $this->markTestSkipped("Test not implemented yet");
    }

    public function testListDatasetPDP()
    {
        $this->markTestSkipped("Test not implemented yet");
    }

    public function testCreateDatasetPDP()
    {
        $this->markTestSkipped("Test not implemented yet");
    }
}