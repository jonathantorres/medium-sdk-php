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
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Ask medium for an access token
     * using the provided authorization code.
     *
     * @param string $authorizationCode
     * @param string $clientId
     * @param string $clientSecret
     * @param string $redirectUrl
     *
     * @return string
     */
    public function requestAccessToken($authorizationCode, $clientId, $clientSecret, $redirectUrl)
    {
        $client = new GuzzleClient([
            'base_uri' => $this->url,
            'exceptions' => false,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Accept-Charset' => 'utf-8',
            ],
        ]);

        $data = [
            'form_params' => [
                'code' => $authorizationCode,
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'grant_type' => 'authorization_code',
                'redirect_uri' => $redirectUrl,
            ],
        ];

        $response = $client->request('POST', 'tokens', $data);

        return json_decode($response->getBody())->access_token;
    }

    /**
     * Set up client to make authenticated requests.
     *
     * @param string $accessToken
     *
     * @todo Move this somewhere else ;)
     *
     * @return void
     */
    public function setUpToMakeRequests($accessToken)
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
