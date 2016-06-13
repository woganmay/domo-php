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
    $client->API->DataSet->export($id)
    
There are some shorthand methods being worked on. For the moment:

    $client->createDataSet($name, $pathToCSVFile)
    
# Contributing

Pull requests accepted!

# Licensing

MIT

This is an unofficial implementation and is not supported by Domo.