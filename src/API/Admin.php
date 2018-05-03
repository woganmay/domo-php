<?php

namespace WoganMay\DomoPHP\API;

/**
 * DomoPHP Admin.
 *
 * Utility methods for working with the admin
 *
 * @author     Wogan May <wogan.may@gmail.com>
 * @link       https://github.com/woganmay/domo-php
 */
class Admin
{
    private $Client = null;

    /**
     * oAuth Client ID.
     *
     * The Client ID obtained from developer.domo.com
     *
     * @param \WoganMay\DomoPHP\Client $APIClient An instance of the API Client
     */
    public function __construct(\WoganMay\DomoPHP\Client $APIClient)
    {
        $this->Client = $APIClient;
    }

    /**
     * @param $input Convert ISO date to epoch (or leave epoch unchanged)
     * @return false|int
     */
    private function parseISOtoEpoch($input)
    {
        return (strpos($input, "-") === FALSE) ? $input : strtotime($input);
    }

    /**
     * @param array $options Query options
     * @return object
     * @throws \Exception
     */
    public function getActivityLog($options = [])
    {
        $options = array_merge([
            "limit" => 50,
            "offset" => 0
        ], $options);

        // Parse start/end dates
        if (isset($options['start'])) $options['start'] = $this->parseISOtoEpoch($options['start']);
        if (isset($options['end']))   $options['end']   = $this->parseISOtoEpoch($options['end']);

        $string = http_build_query($options);

        return $this->Client->getJSON("/v1/audit?$string");
    }
}