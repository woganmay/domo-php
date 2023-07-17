<?php

use PHPUnit\Framework\TestCase;
use WoganMay\DomoPHP\Client;

final class SimpleServiceTest extends TestCase
{
    public function testCreateDataset()
    {
        $client = new Client();

        $hash = uniqid();

        $name = "Test SimpleDataset {$hash}";
        $description = "Created via domo-api at " . date("Y-m-d H:i:s");

        $dataset = $client->simple()->create($name, $description);

        $this->assertEquals($name, $dataset->name);
    }

    public function testPopulateDataset()
    {
        $client = new Client();

        $hash = uniqid();

        // Create a dataset that we can populate
        $dataset = $client->simple()->create("Test PopulateDataset {$hash}");

        // Note: Should be an array of key/value pairs ("objects")
        // The first "object"'s keys will determine the headers for the entire set.
        $data = [
            [ 'Header 1' => 'Row 1', 'Header 2' => 'Row 2', 'Header 3' => 'Row 3' ],
            [ 'Header 1' => 'Row 4', 'Header 2' => 'Row 5', 'Header 3' => 'Row 6' ],
        ];

        // Populate the dataset
        $client->simple()->populate($dataset->id, $data);

        // Grab the imported data and ensure it arrived as expected
        $data = $client->dataSet()->export($dataset->id);

        $this->assertStringContainsString("Header 1,Header 2,Header 3", $data);

    }
}