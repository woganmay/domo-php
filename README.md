# domo-php

Unofficial PHP library to interact with the Domo.com APIs.

#### Installation

Install via composer:

    composer require woganmay/domo-php
    
Then read the documents on http://domo-php.readthedocs.io/en/latest/

If you get stuck, ask for help in the support chat at: https://spectrum.chat/domo-php
   
#### Contributing

Thanks for considering a contribution! 

The purpose of this library is to wrap all the Domo endpoints as per the developer documentation, and account for any edge cases. The Helpers are for general-purpose streamlining of common operations (not specific logic for any single industry, framework or application).

Finally, this library is still under active development until it reaches a parity release with the Domo API. If you're having issues, please ask questions in the Spectrum chat (linked above), or lodge a GitHub issue. Pull requests will only be accepted if they're written in a consistent style with the existing code, and maintain parity with the [documented APIs](https://developer.domo.com/docs/api-status/api-status) provided by Domo.

| Capability | Service | Documentation |
| --- | :---: | :---: |
| DataSets     | Complete | [Complete](http://domo-php.readthedocs.io/en/latest/services.html#datasets) (0.1.0) |
| Groups       | Complete | [Complete](http://domo-php.readthedocs.io/en/latest/services.html#groups) (0.2.0) |
| Pages        | Complete | [Complete](http://domo-php.readthedocs.io/en/latest/services.html#pages) (0.2.0) |
| Users        | Complete | [Complete](http://domo-php.readthedocs.io/en/latest/services.html#users) (0.2.0) |
| Activity Log | Working  | [Complete](http://domo-php.readthedocs.io/en/latest/services.html#activity-logs) (0.2.0) |
| Streams      | Working  | Working (0.2.1) |

#### Licensing

MIT