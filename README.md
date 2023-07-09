# domo-php

Unofficial PHP library to interact with the Domo.com APIs. MIT License.

## ❔ About this project

Domo.com offers a range of Platform APIs, versioned at v1. This library aims to wrap all of those endpoints and make 
them easily usable via PHP. The library was initially built in 2018 (back when the APIs were still in beta), and as of
July 2023, this library is being overhauled to take into account new endpoints, methods and scopes.

If you're using this library in your project and get stuck, you can get help by:

* File a new issue with reproduction steps: https://github.com/woganmay/domo-php/issues/new
* Email the project maintainer at [wogan.may@gmail.com](mailto:wogan.may@gmail.com) for direct support

## ⚠️ About v0.2.2

As of `v0.2.2` (released 8 July 2023) this library has been downloaded over 10,000 times. This version of the library
supported most of the APIs available in 2018, and included a few Helpers for common tasks. Moving forward, there will
be breaking changes to this project, including:

* The Helpers will be removed - that functionality is better suited for an independent library
* The client will be refactored to require PHP 8.1 at a minimum, and take advantage of newer language features
* The DomoPHP client will be updated to implement all of Domo's API methods as of July 2023

If you don't want to deal with any breaking changes just yet, the recommendation is to lock the dependency to this 
specific version in your composer.json:

    "require": {
        "woganmay/domo-php": "v0.2.2"
    }

The old approach under `v0.2.2` won't be supported in future versions. As of `v0.3` and up, the library will use a different pattern for 
the objects, and the way you call the methods will work differently. 

## 🛣️ Road to v1.0.0

The purpose of this library is to maintain parity with the documented APIs on Domo's Developer site. All client code is
being re-implemented from the ground up. The roadmap is currently as follows:

| Capability             | Targeted Version |         Status         | Notes                                    |
|------------------------|------------------|:----------------------:|------------------------------------------|
| Activity Log API       | v0.3.0           | 1/1 methods (100%) ✅ |                                          |
| User API               | v0.3.0           | 5/5 methods (100%) ✅ |                                          |
| DataSet API            | v0.3.0           | 8/13 methods (61%) ⌛  | The "export" method is not supported yet |
| Simple API             | v0.4.0           | 0/2 methods (0%) ⏸️  |                                          |
| Stream API             | v0.4.0           | 0/12 methods (0%) ⏸️  |                                          |
| Account API            | v0.5.0           | 0/8 methods (0%) ⏸️  |                                          |
| Group API              | v0.5.0           | 0/8 methods (0%) ⏸️  |                                          |
| Page API               | v0.6.0           | 0/9 methods (0%) ⏸️  |                                          |
| Embed Token API        | v0.6.0           | 0/2 methods (0%) ⏸️  |                                          |
| Projects and Tasks API | v0.7.0           | 0/21 methods (0%) ⏸️  |                                          |

Domo Documentation URL: https://developer.domo.com/portal/8ba9aedad3679-ap-is

Once all services have been implemented and are testable, a `v1.0.0` release will be done, which should be the last 
major release for quite a while! New releases will only happen as changes happen to the API, or as dependency and
security updates are handled.

## ⚙️ Installation

Ensure you have a valid Domo account, and generate an access token on the [developer.domo](https://developer.domo.com/new-client) site

Install via composer:

    composer require woganmay/domo-php
    
Once loaded, you'll be able to create a new DomoPHP object as follows:

```php
use WoganMay\DomoPHP\Client;

// Create a new instance by passing in the client ID and Secret yourself
$client = new Client("your-client-id", "your-client-secret");

// Or, if you can set environment variables (DOMO_CLIENT_ID and DOMO_CLIENT_SECRET), the Client
// can read those, letting you simply do this:
$client = new Client();

// That's the recommended approach when using a framework (like Laravel) that reads .env variables into
// your environment! You can now call API methods via the $client object, for eg:
$allUsers = $client->user()->getList();

// Every API has a helper proxy method, as follows:
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
```

## 📝 Example: Create and populate a dataset

This approach would work for simple, small CSV loads - maybe 1,000 records total. For larger loads, Domo recommends using the Stream API instead.

```php
use WoganMay\DomoPHP\Client;

$client = new Client();

// The domo-php library expects key-value pairs, where the keys are the column headers, and the values
// are the data type. Valid types are: STRING, DECIMAL, LONG, DOUBLE, DATE, DATETIME
$schema = [
    'id' => 'LONG',
    'name' => 'STRING',
    'created_at' => 'DATETIME'
];

// This will create a new, empty dataset with our schema
$dataset = $client->dataSet()->create("My new dataset", $schema);

// We'll build a simple set of CSV data, though in real life you'd be reading this directly from a file,
// using file_get_contents() or similar. Domo expects \n line endings, and for the first line of the provided
// data to include the CSV headers, matching the name, casing and order of the schema declared earlier.
$csv = "id,name,created_at\n";
$csv .= "1,John Doe,2023-07-07 13:00:00\n";
$csv .= "2,Jane Doe,2023-07-08 14:00:00\n";
$csv .= "3,Bob Smith,2023-07-09 15:00:00\n";

// We can now import data directly into the dataset:
$importResult = $client->dataSet()->import($dataset->id, $csv);

// The import process can take up to 10 seconds to complete on Domo's side, so within 10 seconds after
// completing the above, we should now be able to query our data back out. This query would return two rows
// of data, based on the sample loaded above:
$queryResult = $client->dataSet()->query($dataset->id, "SELECT * FROM table WHERE id >= 2");

```
