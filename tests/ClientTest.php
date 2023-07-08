<?php

use PHPUnit\Framework\TestCase;
use WoganMay\DomoPHP\Client;

final class ClientTest extends TestCase
{
    public function testCreateFromEnv()
    {
        $client = new Client();

        $this->assertInstanceOf(Client::class, $client);

        // Note: This test assumes that the DOMO_CLIENT_ID and DOMO_CLIENT_SECRET values
        // in the phpunit.xml file are valid, and point to a real client that is authorized
        // for all scopes (data, workflow, audit, buzz, user, account, dashboard)
        $this->assertTrue($client->connector()->test());

    }

    public function testCreateFromParams()
    {
        $client = new Client("invalid-client-id", "invalid-client-secret");

        $this->assertInstanceOf(Client::class, $client);

        // Note: This test is designed to break, intentionally, to confirm that passing in values at new Client()
        // get carried through to the connector.
        $this->assertFalse($client->connector()->test());

    }
}