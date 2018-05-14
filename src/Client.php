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
class Client
{
    /**
     * HTTP Client.
     *
     * Currently a Guzzle client, but any PSR-compatible web client will do
     *
     * @var \GuzzleHttp\Client
     */
    public $WebClient;

    /**
     * oAuth Client ID.
     *
     * The Client ID obtained from developer.domo.com
     *
     * @var string
     */
    private $client_id = null;

    /**
     * oAuth Client Secret.
     *
     * The Client Secret obtained from developer.domo.com
     *
     * @var string
     */
    private $client_secret = null;

    /**
     * oAuth Access Token.
     *
     * Set by the refrehToken() method
     *
     * @var string
     */
    private $access_token = null;

    /**
     * oAuth Authentication Context.
     *
     * The entire response from the authentication call, has some additional
     * metadata
     *
     * @var array
     */
    private $context = null;

    /**
     * Token Expiry.
     *
     * The UNIX time at which the token will have expired
     *
     * @var int
     */
    private $expires_at = null;

    /**
     * Service properties
     */
    public $DataSet;
    public $User;
    public $Group;
    public $Page;
    public $Admin;
    public $Stream;

    /**
     * Base URL to talk to the API.
     */
    private $DOMO_Base_URI = 'https://api.domo.com/';

    /**
     * The URL to the authentication endpoint.
     */
    private $DOMO_Token_Endpoint = '/oauth/token?grant_type=client_credentials';

    /**
     * @var array The list of scopes to authorize for
     */
    private $DOMO_Scopes = ['data', 'audit', 'user', 'dashboard'];

    /**
     * Constructor.
     *
     * @param string $client_id     Domo Client ID
     * @param string $client_secret Domo Client Secret
     * @param array  $scopes        Domo scopes to authorize (data, audit, user, dashboard)
     */
    public function __construct($client_id, $client_secret, $scopes = [])
    {
        // Use CURL to handle requests - it's nicer
        $handler = new \GuzzleHttp\Handler\CurlHandler();
        $stack = \GuzzleHttp\HandlerStack::create($handler);

        // Single instance of Guzzle we'll use for everything
        $this->WebClient = new \GuzzleHttp\Client([
            'base_uri' => $this->DOMO_Base_URI,
            'handler'  => $stack,
        ]);

        $this->client_id = $client_id;
        $this->client_secret = $client_secret;

        // By default we authorize for everything, but if a
        // list is provided, limit to that.
        if (!empty($scopes)) $this->setScopes($scopes);

        // Services
        $this->DataSet = new API\DataSet($this);
        $this->Group   = new API\Group($this);
        $this->User    = new API\User($this);
        $this->Page    = new API\Page($this);
        $this->Admin   = new API\Admin($this);
        $this->Stream  = new API\Stream($this);
    }

    /**
     * Sets the list of scopes, validating against the full
     * list that Domo actually supports.
     *
     * @param array $scopes The list of scopes to authorize for
     */
    private function setScopes($scopes)
    {
        $validScopes = $this->DOMO_Scopes;
        $desiredScopes = [];

        foreach($scopes as $scope)
            if (in_array($scope, $validScopes))
                $desiredScopes[] = $scope;

        $this->DOMO_Scopes = $desiredScopes;
    }

    /**
     * Get Token.
     *
     * Gets and/or refreshes the access token
     *
     * @return string An active access token
     */
    public function getToken()
    {

        $fullURL = sprintf("%s&scopes=%s", $this->DOMO_Token_Endpoint, implode(",", $this->DOMO_Scopes));

        // Ensure we have a valid authentication token
        if ($this->access_token == null || time() >= $this->expires_at) {
            // We need to refresh the token
            $result = $this->WebClient->request('GET', $fullURL, ['auth' => [$this->client_id, $this->client_secret]]);

            if ($result->getStatusCode() == 200) {
                $this->context = json_decode($result->getBody());

                // Set the time when the token will expire
                $this->expires_at = time() + $this->context->expires_in - 60;
                $this->access_token = $this->context->access_token;
            }
        }

        return $this->access_token;
    }

    /**
     * Post JSON to the API.
     *
     * Send an array as JSON, and read the response
     *
     * @param string $url The relative URL to post to
     * @param array $body The body array to send
     * @return object
     * @throws \Exception
     */
    public function postJSON($url, $body = null)
    {
        $request = [
            'headers' => [
                'Authorization' => 'Bearer '.$this->getToken(),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ]
        ];

        if ($body !== null) $request['json'] = $body;

        $response = $this->WebClient->post($url, $request);

        // Handle server response
        switch ($response->getStatusCode()) {
            case 200: // Request successful
            case 201: // New resource created, OK
            case 204: // HTTP No Content

                return json_decode($response->getBody());

            default:

                throw new \Exception($response->getBody());
        }
    }

    /**
     * @param string $url The relative API endpoint to PUT to
     * @param array $body Array of fields to PUT
     * @return object
     * @throws \Exception
     */
    public function putJSON($url, $body = null)
    {
        $request = [
            'headers' => [
                'Authorization' => 'Bearer '.$this->getToken(),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ]
        ];

        if ($body !== null) $request['json'] = $body;

        $response = $this->WebClient->put($url, $request);

        // Handle server response
        switch ($response->getStatusCode()) {
            case 200: // Request successful
            case 201: // New resource created, OK
            case 204: // No Content (blank PUT or DELETE succeeded)

                return json_decode($response->getBody());

            default:

                // Unknown result code!
                throw new \Exception($response->getBody());
        }
    }

    /**
     * @param string $url The URL to PUT to
     * @param string $csv The CSV content
     * @return mixed
     * @throws \Exception
     */
    public function putCSV($url, $csv)
    {
        $request = [
            'headers' => [
                'Authorization' => 'Bearer '.$this->getToken(),
                'Content-Type' => 'text/csv',
                'Accept' => 'application/json'
            ],
            'body' => $csv
        ];

        $response = $this->WebClient->put($url, $request);

        switch($response->getStatusCode())
        {
            case 200: // Request successful
                return json_decode($response->getBody());

            default:
                throw new \Exception($response->getBody());

        }

    }

    /**
     * Get JSON from the API
     *
     * Send an array as JSON, and read the response
     *
     * @param string $url The relative URL to get
     * @return object
     * @throws \Exception
     */
    public function getJSON($url)
    {
        $response = $this->WebClient->get($url, [
            'headers' => [
                'Authorization' => 'Bearer '.$this->getToken(),
            ],
        ]);

        // Handle server response
        switch ($response->getStatusCode()) {
            case 200:
                // Got the resource
                return json_decode($response->getBody());
            default:
                // Unknown result code!
                throw new \Exception($response->getBody());
        }
    }

    /**
     * Delete a resource
     * @param $url URL to DELETE
     * @return bool
     * @throws \Exception
     */
    public function delete($url)
    {
        $response = $this->WebClient->delete($url, [
            'headers' => [
                'Authorization' => 'Bearer '.$this->getToken(),
            ],
        ]);

        // Handle server response
        return $response->getStatusCode() == 204;
    }
}
