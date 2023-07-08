# domo-php

Unofficial PHP library to interact with the Domo.com APIs. MIT License.

## Note on v0.2.2

This library was last actively maintained in 2018. `v0.2.2` was released on 8 July 2023, and draws a line under how the
project was run previously.

## Installation

Ensure you have a valid Domo account, and generate an access token on the [developer.domo](https://developer.domo.com/new-client) site

Install via composer:

    composer require woganmay/domo-php
    
Once required, you'll be able to create a new DomoPHP object as follows:

    use WoganMay\DomoPHP\DomoPHP;

    // v0.2.2 and v0.3+
    $client = new DomoPHP("your-client-id", "your-client-secret");

    // (v0.2.2 only) You can now call API methods via the $client->API object, for eg:
    $allUsers = $client->API->User->getList();

    // (v0.2.2 only) Every API has a proxy object, as follows:
    $client->API->DataSet; // DataSet API 
    $client->API->Group; // Group API 
    $client->API->User; // User API 
    $client->API->Page; // Page API 
    $client->API->Admin; // Admin API 
    $client->API->Stream; // Stream API 

    // (v0.3+ only) You can now call API methods via the $client object, for eg:
    $allUsers = $client->user()->getList();

    // (v0.3+ only) Every API has a helper proxy method, as follows:
    $client->account(); // Account API
    $client->activityLog(); // Activity Log API
    $client->dataSet(); // DataSet API
    $client->embedToken(); // Embed Token API
    $client->group(); // Group API
    $client->page(); // Page API
    $client->projects(); // Projects and Tasks API
    $client->simple(); // Simple API
    $client->stream(); // Stream API
    $client->user(); // User API

For v0.2.2, the documentation is at: http://domo-php.readthedocs.io/en/latest/

For v0.3.0, no documentation is available yet - each of the service methods will have built-in docblocks.

## Roadmap for v0.3

The v0.3 release of this library will make a few breaking changes:

* The Helpers will be removed - that functionality is better suited for an independent library
* The client will be refactored to require PHP 8.1 at a minimum, and take advantage of newer language features
* The DomoPHP client will be updated to implement all of Domo's API methods as of July 2023, which are:

| Capability             |             Status             |
|------------------------|:------------------------------:|
| Account API            |  0/8 methods implemented (0%)  | 
| Activity Log API       | 1/1 methods implemented (100%) |
| DataSet API            | 0/13 methods implemented (0%)  |
| Embed Token API        |  0/2 methods implemented (0%)  |
| Group API              |  0/8 methods implemented (0%)  |
| Page API               |  0/9 methods implemented (0%)  |
| Projects and Tasks API | 0/21 methods implemented (0%)  |
| Simple API             |  0/2 methods implemented (0%)  |
| Stream API             | 0/12 methods implemented (0%)  |
| User API               | 5/5 methods implemented (100%) |

Domo Documentation URL: https://developer.domo.com/portal/8ba9aedad3679-ap-is

## Support

Need help? This library is actively maintained, and you can either:

* File a new issue with reproduction steps: https://github.com/woganmay/domo-php/issues/new
* Email the project maintainer at wogan.may@gmail.com for direct support