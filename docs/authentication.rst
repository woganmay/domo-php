==============
Authentication
==============

The domo-php library needs a Client ID and Secret from the Domo Developer site. Log in at https://developer.domo.com/manage-clients to create a new client.

Once you have the ID and secret, you can use them to create a new instance of the API client::

    use WoganMay\DomoPHP\DomoPHP;

    $client_id     = "guid";
    $client_secret = "sha256";

    $client = new DomoPHP($client_id, $client_secret);

The new client should authorize all 4 scopes (data, audit, user and dashboard). If you want to use fewer scopes, you should indicate which you've created a client for as the third parameter, ie::

    $client = new DomoPHP($client_id, $client_secret, ['data', 'user']);

