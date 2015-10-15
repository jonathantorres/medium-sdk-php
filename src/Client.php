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
        $data = [
            'form_params' => [
                'code' => $authorizationCode,
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'grant_type' => 'authorization_code',
                'redirect_uri' => $redirectUrl,
            ],
        ];

        return $this->retrieveAccessToken($data);
    }

    /**
     * Request a new access token using the refresh token.
     *
     * @param string $refreshToken
     * @param string $clientId
     * @param string $clientSecret
     *
     * @return string
     */
    public function exchangeRefreshToken($refreshToken, $clientId, $clientSecret)
    {
        $data = [
            'form_params' => [
                'refresh_token' => $refreshToken,
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'grant_type' => 'refresh_token',
            ],
        ];

        return $this->retrieveAccessToken($data);
    }

    /**
     * Authenticate client to make authenticated requests.
     *
     * @param string $accessToken
     *
     * @return void
     */
    public function authenticate($accessToken)
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

    /**
     * Retrieve an access token.
     *
     * @param array $data
     *
     * @return string
     */
    private function retrieveAccessToken(array $data)
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

        $response = $client->request('POST', 'tokens', $data);

        return json_decode($response->getBody())->access_token;
    }
}
