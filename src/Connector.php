<?php

namespace WoganMay\DomoPHP;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;

class Connector
{

    private HttpClient $client;

    private ?string $access_token = null;
    private ?\stdClass $oAuthContext = null;
    private ?int $expires_at = null;

    public function __construct(private string $clientId, private string $clientSecret)
    {
        $this->client = new HttpClient([
            'base_uri' => 'https://api.domo.com/',
            'handler'  => HandlerStack::create(new CurlHandler()),
        ]);
    }

    /**
     * @throws GuzzleException
     */
    public function getToken() : string
    {

        // We're assuming the token can hit all scopes
        $scopes = ['data', 'workflow', 'audit', 'buzz', 'user', 'account', 'dashboard'];

        $fullURL = sprintf("/oauth/token?grant_type=client_credentials&scopes=%s", implode(",", $scopes));

        // Ensure we have a valid authentication token
        if ($this->access_token == null || time() >= $this->expires_at) {
            // We need to refresh the token
            $result = $this->client->request('GET', $fullURL, ['auth' => [$this->clientId, $this->clientSecret]]);

            if ($result->getStatusCode() == 200) {
                $this->oAuthContext = json_decode($result->getBody());

                // Set the time when the token will expire
                $this->expires_at = time() + $this->oAuthContext->expires_in - 60;
                $this->access_token = $this->oAuthContext->access_token;
            }
        }

        return $this->access_token;
    }

    public function test() : bool
    {
        try
        {
            $this->getToken();
            return true;
        }
        catch(GuzzleException $ex)
        {
            return false;
        }
    }

    /**
     * Post JSON to the API.
     *
     * Send an array as JSON, and read the response
     *
     * @param string $url The relative URL to post to
     * @param array $body The body array to send
     * @return mixed
     * @throws \Exception
     * @throws GuzzleException
     */
    public function postJSON(string $url, array $body = []): mixed
    {
        $request = [
            'headers' => [
                'Authorization' => 'Bearer '.$this->getToken(),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ]
        ];

        if ($body !== []) $request['json'] = $body;

        $response = $this->client->post($url, $request);

        // Handle server response
        return match ($response->getStatusCode()) {
            200, 201, 204 => json_decode($response->getBody()),
            default => throw new \Exception($response->getBody()),
        };
    }

    public function putCSV(string $url, string $csv) : bool
    {
        $request = [
            'headers' => [
                'Authorization' => 'Bearer '.$this->getToken(),
                'Content-Type' => 'text/csv',
                'Accept' => 'application/json'
            ],
            'body' => $csv
        ];

        $response = $this->client->put($url, $request);

        // Handle server response
        return match ($response->getStatusCode()) {
            200, 201 => false,
            204 => true,
            default => throw new \Exception($response->getBody()),
        };
    }

    /**
     * @param string $url The relative API endpoint to PUT to
     * @param array $body Array of fields to PUT
     * @return object
     * @throws \Exception
     */
    public function putJSON(string $url, array $body = []) : mixed
    {
        $request = [
            'headers' => [
                'Authorization' => 'Bearer '.$this->getToken(),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ]
        ];

        if ($body !== []) $request['json'] = $body;

        $response = $this->client->put($url, $request);

        // Handle server response
        return match ($response->getStatusCode()) {
            200, 201, 204 => json_decode($response->getBody()),
            default => throw new \Exception($response->getBody()),
        };
    }

    /**
     * Get JSON from the API
     *
     * Send an array as JSON, and read the response
     *
     * @param string $url The relative URL to get
     * @return object
     * @throws \Exception
     * @throws GuzzleException
     */
    public function getJSON(string $url, ?array $params = []) : mixed
    {
        $fullUrl = sprintf("%s?%s", $url, http_build_query($params));

        $response = $this->client->get($fullUrl, [
            'headers' => [
                'Authorization' => 'Bearer '. $this->getToken(),
            ],
        ]);

        // Handle server response
        return match ($response->getStatusCode()) {
            200 => json_decode($response->getBody()),
            default => throw new \Exception($response->getBody()),
        };
    }

    public function getCSV(string $url, array $params = []) : string
    {
        $fullUrl = sprintf("%s?%s", $url, http_build_query($params));

        $request = [
            'headers' => [
                'Authorization' => 'Bearer '.$this->getToken(),
                'Content-Type' => 'application/json',
                'Accept' => 'text/csv'
            ]
        ];

        $response = $this->client->get($fullUrl, $request);

        // Handle server response
        return match ($response->getStatusCode()) {
            200, 201, 204 => $response->getBody()->getContents(),
            default => throw new \Exception($response->getBody()),
        };
    }

    public function delete($url) : bool
    {
        $response = $this->client->delete($url, [
            'headers' => [
                'Authorization' => 'Bearer '.$this->getToken(),
            ],
        ]);

        // Handle server response
        return $response->getStatusCode() == 204;
    }
}