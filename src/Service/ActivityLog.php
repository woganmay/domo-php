<?php

namespace WoganMay\DomoPHP\Service;

use GuzzleHttp\Exception\GuzzleException;
use WoganMay\DomoPHP\Client;

class ActivityLog
{
    public function __construct(private Client $client) { }

    /**
     * @param int $startTime REQUIRED: The start time(milliseconds) of when you want to receive log events
     * @param int $limit The maximum number of events you want to retrieve(default is 50, maximum of 1000)
     * @param int $offset The offset location of events you retrieve(default is 0)
     * @param string|null $user The Id of the user
     * @param int|null $endTime The end time(milliseconds) of when you want to receive log events
     * @return array The array of audit log objects
     * @throws GuzzleException
     */
    public function get(int $startTime, int $limit = 50, int $offset = 0, ?string $user = null, ?int $endTime = null) : array
    {
        return $this->client->connector()->getJSON('/v1/audit', [
            'user' => $user,
            'start' => $startTime,
            'end' => $endTime,
            'limit' => $limit,
            'offset' => $offset
        ]);
    }
}