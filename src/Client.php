<?php

namespace JonathanTorres\MediumSdk;

use GuzzleHttp\Client as GuzzleClient;

class Client
{
    /**
     * Medium api url
     *
     * @var string
     */
    private $url = 'https://api.medium.com/v1/';

    /**
     * Guzzle http client
     *
     * @var GuzzleHttp\Client
     */
    private $client;

    /**
     * Initialize.
     *
     * @param string $accessToken
     *
     * @return void
     */
    public function __construct($accessToken)
    {
        $this->client = new GuzzleClient([
            'base_uri' => $this->url,
            'exceptions' => false,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Accept-Charset' => 'utf-8',
                'Authorization' => 'Bearer ' . $accessToken,
            ],
        ]);
    }

    /**
     * Make a request to medium's api.
     *
     * @param string $method
     * @param string $endpoint
     * @param array $data
     *
     * @return StdClass
     */
    public function makeRequest($method, $endpoint, array $data = [])
    {
        $response = $this->client->request($method, $endpoint, $data);

        return json_decode($response->getBody());
    }
}
