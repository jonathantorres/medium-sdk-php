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
     * Ask medium for the access and refresh token
     * using the provided authorization code.
     *
     * @todo  This requestTokens() method uses some repeated code
     *        exchangeRefreshToken(), refactor to make the code less redundant and re-usable
     *
     * @param string $authorizationCode
     * @param string $clientId
     * @param string $clientSecret
     * @param string $redirectUrl
     *
     * @return StdClass
     */
    public function requestTokens($authorizationCode, $clientId, $clientSecret, $redirectUrl)
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

        return json_decode($response->getBody());
    }

    /**
     * Request a new access token using the refresh token.
     *
     * @todo  This exchangeRefreshToken() method uses some repeated code
     *        requestTokens(), refactor to make the code less redundant and re-usable
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
}
