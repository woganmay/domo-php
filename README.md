# domo-php

Unofficial PHP library to interact with the Domo.com APIs. MIT License.

#### Installation

Ensure you have a valid Domo account, and generate an access token on the [developer.domo](https://developer.domo.com/new-client) site

Install via composer:

    composer require woganmay/domo-php
    
Then read the documents on http://domo-php.readthedocs.io/en/latest/

If you get stuck, ask for help in the support chat at: https://spectrum.chat/domo-php
   
#### Contributing

Thanks for considering a contribution! 

The purpose of this library is to wrap all the Domo endpoints as per the developer documentation, and account for any edge cases. The Helpers are for general-purpose streamlining of common operations (not specific logic for any single industry, framework or application).

Pull requests will only be accepted if they're written in a consistent style with the existing code, and maintain parity with the [documented APIs](https://developer.domo.com/docs/api-status/api-status) provided by Domo.

| Capability | Service | Documentation |
| --- | :---: | :---: |
| DataSets     | Complete | [Complete](http://domo-php.readthedocs.io/en/v0.2.1/api.html#datasets) (0.1.0) |
| Groups       | Complete | [Complete](http://domo-php.readthedocs.io/en/v0.2.1/api.html#groups) (0.2.0) |
| Pages        | Complete | [Complete](http://domo-php.readthedocs.io/en/v0.2.1/api.html#pages) (0.2.0) |
| Users        | Complete | [Complete](http://domo-php.readthedocs.io/en/v0.2.1/api.html##users) (0.2.0) |
| Activity Log | Complete | [Complete](http://domo-php.readthedocs.io/en/v0.2.1/api.html#activity-logs) (0.2.0) |
| Streams      | Complete | [Complete](http://domo-php.readthedocs.io/en/v0.2.1/api.html#streams) (0.2.1) |

Last updated: 18 May 2018