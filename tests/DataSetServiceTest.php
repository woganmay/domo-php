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
        $client = new Client();

        $hash = uniqid();

        $csv = "Field\nRow 1\nRow 2\nRow 3";

        // Create a new dataset, so we have a target for exporting
        $dataset = $client->dataSet()->create("Test ExportDataset {$hash}", [ 'Field' => 'STRING' ]);
        $client->dataSet()->import($dataset->id, $csv);

        // Give Domo a chance to run whatever internal processes are required
        sleep(10);

        // We should now be able to export this
        $exportResult = $client->dataSet()->export($dataset->id);

        // Domo adds a trailing newline to exported data, so comparing the
        // trim() value on both sides should work. In addition, this also
        // validates that the header is present in the downloaded file.
        $this->assertEquals(trim($csv), trim($exportResult));

    }

    public function testListDatasetPDP()
    {
        $client = new Client();

        $hash = uniqid();

        // Create a new dataset, so we have a target for listing the PDP
        $dataset = $client->dataSet()->create("Test ListDatasetPDP {$hash}", [ 'Field' => 'STRING' ]);

        // Should now be able to get the PDP on it (an empty array)
        $pdp = $client->dataSet()->listPDP($dataset->id);

        // A new dataset should have 1x PDP on it, a default of "All Rows" that has no filters or users
        // attached to it.
        $this->assertIsArray($pdp);
        $this->assertEquals("All Rows", $pdp[0]->name);
        $this->assertEmpty($pdp[0]->filters);
        $this->assertEmpty($pdp[0]->users);

    }

    public function testCreateDatasetPDP()
    {
        $client = new Client();

        $hash = uniqid();

        // Create a new dataset, so we have a target for creating the PDP
        $dataset = $client->dataSet()->create("Test CreateDatasetPDP {$hash}", [ 'Field' => 'STRING' ]);

        // The PDP system adds filters to fields, but Domo doesn't seem to understand that fields are present
        // if the dataset is not populated, so let's add some data to it.
        $client->dataSet()->import($dataset->id, "Field\nRow 1\nRow 2\nRow 3");

        // Give Domo a chance to run whatever internal processes are required
        sleep(10);

        // Now, attach a PDP to the first available user
        $firstUser = $client->user()->list(1);

        $this->assertEquals(865213262, $firstUser[0]->id);

        $users = [ $firstUser[0]->id ];

        // Allow the user to only see data where 'Field' = 'Row 1'
        $filters = [[
            'column' => 'Field',
            'operator' => 'EQUALS',
            'values' => [
                'Row 1'
            ]
        ]];

        $newPDP = $client->dataSet()->createPDP($dataset->id, "Test PDP #1 - {$hash}", $filters, $users);

        // If we have a PDP object with at least 1 user in it, we're good
        $this->assertEquals("Test PDP #1 - {$hash}", $newPDP->name);
        $this->assertNotEmpty($newPDP->users);

    }

    public function testGetDatasetPDP()
    {

        $client = new Client();

        $hash = uniqid();

        // Create a new dataset, so we have a target for getting the PDP
        $dataset = $client->dataSet()->create("Test GetDatasetPDP {$hash}", [ 'Field' => 'STRING' ]);
        $client->dataSet()->import($dataset->id, "Field\nRow 1\nRow 2\nRow 3");

        // Give Domo a chance to run whatever internal processes are required
        sleep(10);

        // Now, attach a PDP to the first available user
        $firstUser = $client->user()->list(1);
        $filters = [['column' => 'Field', 'operator' => 'EQUALS', 'values' => [ 'Row 1' ] ]];
        $newPDP = $client->dataSet()->createPDP($dataset->id, "Test PDP #1 - {$hash}", $filters, [ $firstUser[0]->id ]);

        // Now try the GET endpoint and ensure we get the same value back
        $retrievedPDP = $client->dataSet()->getPDP($dataset->id, $newPDP->id);

        $this->assertEquals($newPDP->id, $retrievedPDP->id);

    }

    public function testUpdateDatasetPDP()
    {
        $client = new Client();

        $hash = uniqid();

        // Create a new dataset, so we have a target for getting the PDP
        $dataset = $client->dataSet()->create("Test UpdateDatasetPDP {$hash}", [ 'Field' => 'STRING' ]);
        $client->dataSet()->import($dataset->id, "Field\nRow 1\nRow 2\nRow 3");

        // Give Domo a chance to run whatever internal processes are required
        sleep(10);

        // Now, attach a PDP to the first available user
        $firstUser = $client->user()->list(1);
        $secondUser = $client->user()->list(1, 1);
        $filters = [['column' => 'Field', 'operator' => 'EQUALS', 'values' => [ 'Row 1' ] ]];
        $newPDP = $client->dataSet()->createPDP($dataset->id, "Test PDP #1 - {$hash}", $filters, [ $firstUser[0]->id ]);

        // Update the existing PDP to modify everything about it:
        $updates = [

            // Rename the PDP
            'name' => "Test PDP #1-renamed - {$hash}",

            // Change the only filter on it to check for Field=Row 2 instead
            'filters' => [['column' => 'Field', 'operator' => 'EQUALS', 'values' => [ 'Row 2' ] ]],

            // Update the user to someone totally different
            'users' => [ $secondUser[0]->id ]

        ];

        // Do the actual update
        $client->dataSet()->updatePDP($dataset->id, $newPDP->id, $updates);

        // Retrieve the PDP from scratch for comparison
        $retrievedPDP = $client->dataSet()->getPDP($dataset->id, $newPDP->id);

        // Ensure the PDP has updated
        $this->assertEquals("Test PDP #1-renamed - {$hash}", $retrievedPDP->name);
        $this->assertEquals("Row 2", $retrievedPDP->filters[0]->values[0]);
        $this->assertEquals($secondUser[0]->id, $retrievedPDP->users[0]);

    }

    public function testDeleteDatasetPDP()
    {
        $client = new Client();

        $hash = uniqid();

        // Create a new dataset, so we have a target for getting the PDP
        $dataset = $client->dataSet()->create("Test DeleteDatasetPDP {$hash}", [ 'Field' => 'STRING' ]);
        $client->dataSet()->import($dataset->id, "Field\nRow 1\nRow 2\nRow 3");

        // Give Domo a chance to run whatever internal processes are required
        sleep(10);

        // Now, attach a PDP to the first available user
        $firstUser = $client->user()->list(1);
        $filters = [['column' => 'Field', 'operator' => 'EQUALS', 'values' => [ 'Row 1' ] ]];
        $newPDP = $client->dataSet()->createPDP($dataset->id, "Test PDP #1 - {$hash}", $filters, [ $firstUser[0]->id ]);

        // Verify it's been created by listing out the policies on the DataSet - there should be 2:
        $pdpCheck1 = $client->dataSet()->listPDP($dataset->id);
        $this->assertEquals(2, count($pdpCheck1));

        // Now let's delete ours
        $client->dataSet()->deletePDP($dataset->id, $newPDP->id);

        // And confirm it's gone, by re-listing and only seeing 1 PDP on the dataset
        $pdpCheck2 = $client->dataSet()->listPDP($dataset->id);
        $this->assertEquals(1, count($pdpCheck2));

    }

}