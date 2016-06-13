<?php

namespace WoganMay\DomoPHP;

/**
 * DomoPHP API Client
 *
 * The DomoPHP client implements a simple object-based way to access the Domo
 * API.
 * 
 * Currently the client is focused on the Data methods.
 * 
 * @package    DomoPHP
 * @author     Wogan May <wogan.may@gmail.com>
 * @license    MIT
 * @link       https://github.com/woganmay/domo-php
 */

class DomoAPIClient {
    
    /**
     * HTTP Client
     *
     * Currently a Guzzle client, but any PSR-compatible web client will do
     *
     * @var GuzzleHttp\Client
     */
    public $WebClient;
    
    /**
     * oAuth Client ID
     *
     * The Client ID obtained from developer.domo.com
     *
     * @var string
     */
    private $client_id = null;
    
    /**
     * oAuth Client Secret
     *
     * The Client Secret obtained from developer.domo.com
     *
     * @var string
     */
    private $client_secret = null;
    
    /**
     * oAuth Access Token
     *
     * Set by the refrehToken() method
     *
     * @var string
     */
    private $access_token = null;
    
    /**
     * oAuth Authentication Context
     *
     * The entire response from the authentication call, has some additional
     * metadata
     *
     * @var array
     */
    private $context = null;
    
    /**
     * Token Expiry
     *
     * The UNIX time at which the token will have expired
     *
     * @var int
     */
    private $expires_at = null;
    
    /**
     * DataSet Methods
     */
    public $DataSet;
    
    /**
     * Base URL to talk to the API
     */
    private $DOMO_Base_URI = "https://api.domo.com";
    
    /**
     * The URL to the authentication endpoint
     */
    private $DOMO_Token_Endpoint = "/oauth/token?grant_type=client_credentials&scope=data";
    
    /**
     * Constructor
     *
     * @param string $client_id Domo Client ID
     * @param string $client_secret Domo Client Secret
     */
    public function __construct($client_id, $client_secret)
    {
        // Use CURL to handle requests - it's nicer
        $handler = new \GuzzleHttp\Handler\CurlHandler;
        $stack = \GuzzleHttp\HandlerStack::create($handler);
        
        // Single instance of Guzzle we'll use for everything
        $this->WebClient = new \GuzzleHttp\Client([
            'base_uri' => $this->DOMO_Base_URI,
            'handler' => $stack
        ]);
        
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        
        // Function Groups
        $this->DataSet = new DataSet($this);
        
    }
    
    /**
     * Get Token
     * 
     * Gets and/or refreshes the access token
     *
     * @return string An active access token
     */
    public function getToken()
    {
        // Ensure we have a valid authentication token
        if ($this->access_token == null || time() >= $this->expires_at)
        {
            // We need to refresh the token
            $result = $this->WebClient->request('GET', $this->DOMO_Token_Endpoint, ['auth' => [$this->client_id, $this->client_secret]]);
            
            if ($result->getStatusCode() == 200)
            {
                $this->context = json_decode($result->getBody());

                // Set the time when the token will expire                
                $this->expires_at = time() + $this->context->expires_in - 60;
                $this->access_token = $this->context->access_token;
                
            }
        }
        
        return $this->access_token;
    }
    
}