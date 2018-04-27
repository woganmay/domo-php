# domo-php

Unofficial PHP library to interact with the Domo.com APIs

# Installation

Install via composer:

    composer require woganmay/domo-php
    
# Authentication

You'll need a Client ID and Client Secret in order to use the API. You can obtain them here: https://developer.domo.com/new-client

# Usage

Create a new client object using the ID and Secret:

    $id = "...";
    $secret = "...";
    $client = new WoganMay\DomoPHP\DomoPHP($id, $secret);
    
The client will take care of obtaining and refreshing the token.

These methods let you use the Data API directly, which map more or less exactly to the API documentation at https://developer.domo.com/docs/domo-apis/data

    $client->API->DataSet->create($name, $columns, $description = "")
    $client->API->DataSet->getMetaData($id);
    $client->API->DataSet->getList($limit = 10, $offset = 0, $sort = "name", $fields = "all")
    $client->API->DataSet->update($id, $update)
    $client->API->DataSet->delete($id)
    $client->API->DataSet->import($id, $csv)
    $client->API->DataSet->export($id, $csvHeaders = FALSE)
    
There are some shorthand methods being worked on. For the moment:

    $client->createDataSet($name, $pathToCSVFile)
    
# Example: Upload a CSV File

A simple use case - you have a CSV file on disk that you need to push into Domo as a new DataSet. For example, here's a file (input.csv):

    ID,Name,Age,Price,Created,Updated
    1,Jill,27,10.50,2015-01-01,2015-04-01 03:00:10
    2,Jeff,11,99.90,2015-02-01,2015-04-02 01:01:00
    
The file mixes the various datatypes that the API supports - STRING, LONG, DATE, DATETIME and DECIMAL. To upload this into Domo as a new DataSet, create a new client as per the instructions above:

    $client->createDataSet("New DataSet", "input.csv")
    
That will infer the schema based on the first record of data in the file, create the dataset with the schema, and import the CSV file. It will return an object that describes the dataset:

    stdClass Object
    (
        [id] => a455b785-fcee-4d3e-bbed-fcd88ae336a7
        [name] => New DataSet
        [description] => 
        [rows] => 0
        [columns] => 6
        [schema] => stdClass Object
            (
                /* snip */
            )
    
        [owner] => stdClass Object
            (
                /* owner: id and display name */
            )
    
        [dataCurrentAt] => 2016-06-13T21:03:36.065Z
        [createdAt] => 2016-06-13T21:03:35Z
        [updatedAt] => 2016-06-13T21:03:36Z
    )
    
# Contributing

Pull requests accepted!

# Licensing

MIT

This is an unofficial implementation and is not supported by Domo.