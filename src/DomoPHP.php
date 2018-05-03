<?php

namespace WoganMay\DomoPHP;

/**
 * DomoPHP Client.
 *
 * The DomoPHP client implements a simple object-based way to access the Domo
 * API.
 *
 * Currently the client is focused on the Data methods.
 *
 * @author     Wogan May <wogan.may@gmail.com>
 * @license    MIT
 *
 * @link       https://github.com/woganmay/domo-php
 */
class DomoPHP
{
    public $API = null;
    public $Helpers = null;

    public function __construct($client_id, $client_secret)
    {
        $this->API = new Client($client_id, $client_secret);
        $this->Helpers = new Helpers\Helpers;
    }

}
