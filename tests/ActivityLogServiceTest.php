<?php

use PHPUnit\Framework\TestCase;
use WoganMay\DomoPHP\Client;

final class ActivityLogServiceTest extends TestCase
{
    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testGetAuditLogs()
    {
        $client = new Client();

        $logs = $client->activityLog()->get(0);

        $this->assertIsArray($logs);

        // This assertion assumes that there is always at least 1 audit log event in your Domo instance,
        // which should be mostly true!
        $this->assertGreaterThan(0, count($logs));

    }
}