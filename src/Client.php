<?php

namespace WoganMay\DomoPHP;

use WoganMay\DomoPHP\Service\Account;
use WoganMay\DomoPHP\Service\ActivityLog;
use WoganMay\DomoPHP\Service\DataSet;
use WoganMay\DomoPHP\Service\EmbedToken;
use WoganMay\DomoPHP\Service\Group;
use WoganMay\DomoPHP\Service\Page;
use WoganMay\DomoPHP\Service\Projects;
use WoganMay\DomoPHP\Service\Simple;
use WoganMay\DomoPHP\Service\Stream;
use WoganMay\DomoPHP\Service\User;

/**
 * DomoPHP Client.
 *
 * The DomoPHP client implements a simple object-based way to access the Domo
 * API from your PHP project.
 *
 * @author     Wogan May <wogan.may@gmail.com>
 * @license    MIT
 *
 * @link       https://github.com/woganmay/domo-php
 */
class Client
{

    private ?Connector $connector = null;
    private ?Account $account = null;
    private ?ActivityLog $activityLog = null;
    private ?DataSet $dataSet = null;
    private ?EmbedToken $embedToken = null;
    private ?Group $group = null;
    private ?Page $page = null;
    private ?Projects $projects = null;
    private ?Simple $simple = null;
    private ?Stream $stream = null;
    private ?User $user = null;

    /**
     * Constructor.
     *
     * @param string|null $client_id Domo Client ID - leave NULL to read from DOMO_CLIENT_ID
     * @param string|null $client_secret Domo Client Secret - leave NULL to read from DOMO_CLIENT_SECRET
     * @throws \Exception
     */
    public function __construct(?string $client_id = null, ?string $client_secret = null)
    {

        $client_id = $client_id ?? getenv('DOMO_CLIENT_ID');
        $client_secret = $client_secret ?? getenv('DOMO_CLIENT_SECRET');

        // If these cannot be read from .env and are not provided when creating the client, there is no
        // way that the connector below will work, so:
        if ($client_id == null || $client_secret == null)
            throw new \Exception("Please provide a Domo Client ID and Domo Client Secret");

        $this->connector = new Connector($client_id, $client_secret);
        $this->account = new Account($this);
        $this->activityLog = new ActivityLog($this);
        $this->dataSet = new DataSet($this);
        $this->embedToken = new EmbedToken($this);
        $this->group = new Group($this);
        $this->page = new Page($this);
        $this->projects = new Projects($this);
        $this->simple = new Simple($this);
        $this->stream = new Stream($this);
        $this->user = new User($this);

        return $this;

    }

    public function connector() : Connector
    {
        return $this->connector;
    }

    public function account() : Account
    {
        return $this->account;
    }
    public function activityLog() : ActivityLog
    {
        return $this->activityLog;
    }
    public function dataSet() : DataSet
    {
        return $this->dataSet;
    }
    public function embedToken() : EmbedToken
    {
        return $this->embedToken;
    }
    public function group() : Group
    {
        return $this->group;
    }
    public function page() : Page
    {
        return $this->page;
    }
    public function projects() : Projects
    {
        return $this->projects;
    }
    public function simple() : Simple
    {
        return $this->simple;
    }
    public function stream() : Stream
    {
        return $this->stream;
    }
    public function user() : User
    {
        return $this->user;
    }

}
