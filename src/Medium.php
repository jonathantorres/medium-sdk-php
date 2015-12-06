<?php

namespace JonathanTorres\MediumSdk;

use JonathanTorres\MediumSdk\Client;

class Medium
{
    /**
     * Medium api client
     *
     * @var JonathanTorres\MediumSdk\Client
     */
    private $client;

    /**
     * Access token for authenticated requests.
     *
     * @var string
     */
    private $accessToken;

    /**
     * Api client id.
     *
     * @var string
     */
    private $clientId;

    /**
     * Api client secret
     *
     * @var string
     */
    private $clientSecret;

    /**
     * Authentication callback url.
     *
     * @var string
     */
    private $redirectUrl;

    /**
     * State id to prevent request forgery.
     *
     * @var string
     */
    private $state;

    /**
     * Api access scopes.
     *
     * @var string
     */
    private $scopes;

    /**
     * Initialize.
     *
     * @param mixed $credentials
     *
     * @return void
     */
    public function __construct($credentials = null)
    {
        if (!is_null($credentials)) {
            $this->setUpCredentials($credentials);
            $this->setBasicApiClient();
        }
    }

    /**
     * Connect to the api using credentials.
     *
     * @param mixed $credentials
     *
     * @return void
     */
    public function connect($credentials)
    {
        $this->setUpCredentials($credentials);
        $this->setBasicApiClient();
    }

    /**
     * Get the url to authenticate the user to medium.
     *
     * @return string
     */
    public function getAuthenticationUrl()
    {
        $params = [
            'client_id' => $this->clientId,
            'scope' => $this->scopes,
            'state' => $this->state,
            'response_type' => 'code',
            'redirect_uri' => $this->redirectUrl,
        ];

        return 'https://medium.com/m/oauth/authorize?' . http_build_query($params);
    }

    /**
     * Get an access token (authenticate) from the user
     * to make requests to medium's api using the authorization code.
     *
     * @param string $authorizationCode
     *
     * @return void
     */
    public function authenticate($authorizationCode)
    {
        $this->accessToken = $this->client->requestAccessToken(
            $authorizationCode,
            $this->clientId,
            $this->clientSecret,
            $this->redirectUrl
        );

        $this->client->authenticate($this->accessToken);
    }

    /**
     * Request a new access token using the refresh token.
     *
     * @param string $refreshToken
     *
     * @return string
     */
    public function exchangeRefreshToken($refreshToken)
    {
        return $this->client->exchangeRefreshToken(
            $refreshToken,
            $this->clientId,
            $this->clientSecret
        );
    }

    /**
     * Get the current authenticated user object.
     *
     * @return StdClass
     */
    public function getAuthenticatedUser()
    {
        return $this->client->makeRequest('GET', 'me');
    }

    /**
     * Get the specified user publications.
     *
     * @param string $userId
     *
     * @return StdClass
     */
    public function publications($userId)
    {
        return $this->client->makeRequest('GET', 'users/' . $userId . '/publications');
    }

    /**
     * Get the contributors of the specified publication.
     *
     * @param string $publicationId
     *
     * @return StdClass
     */
    public function contributors($publicationId)
    {
        return $this->client->makeRequest('GET', 'publications/' . $publicationId . '/contributors');
    }

    /**
     * Create a post on the authenticated user's profile.
     *
     * @param string $authorId
     * @param array $data
     *
     * @return StdClass
     */
    public function createPost($authorId, array $data)
    {
        $requestData = [
            'form_params' => $data,
        ];

        return $this->client->makeRequest('POST', 'users/' . $authorId . '/posts', $requestData);
    }

    /**
     * Create a post under a publication on the authenticated user's profile.
     *
     * @param string $publicationId
     * @param array $data
     *
     * @return StdClass
     */
    public function createPostUnderPublication($publicationId, array $data)
    {
        $requestData = [
            'form_params' => $data,
        ];

        return $this->client->makeRequest('POST', 'publications/' . $publicationId . '/posts', $requestData);
    }

    /**
     * Upload an image.
     *
     * @param string $image
     * @param string $filename
     *
     * @return StdClass
     */
    public function uploadImage($image, $filename)
    {
        $requestData = [
            'multipart' => [
                [
                    'name' => 'image',
                    'filename' => $filename,
                    'contents' => $image,
                ],
            ]
        ];

        return $this->client->makeRequest('POST', 'images', $requestData);
    }

    /**
     * Get the access token.
     *
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Set the access token.
     *
     * @param string $accessToken
     *
     * @return void
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
        $this->client->authenticate($this->accessToken);
    }

    /**
     * Get the api client id.
     *
     * @return string
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * Get the api client secret.
     *
     * @return string
     */
    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    /**
     * Set the api client instance.
     *
     * @param mixed $client
     *
     * @todo Did this for the sake of testing,
     *       should be a better way to approach this.
     *
     * @return void
     */
    public function setClient($client)
    {
        $this->client = $client;
    }

    /**
     * Setup initial api credentials.
     *
     * @param mixed $credentials
     *
     * @return void
     */
    private function setUpCredentials($credentials)
    {
        if (is_array($credentials)) {
            // using full credentials
            $this->clientId = $credentials['client-id'];
            $this->clientSecret = $credentials['client-secret'];
            $this->redirectUrl = $credentials['redirect-url'];
            $this->state = $credentials['state'];
            $this->scopes = $credentials['scopes'];
        } else {
            // using self issued access token
            $this->accessToken = $credentials;
        }
    }

    /**
     * Minimal setup for medium's api client.
     * Just get it ready to request for access tokens.
     *
     * @return void
     */
    private function setBasicApiClient()
    {
        $this->client = new Client();

        // if using a self issued access token,
        // just authenticate the user right now
        if (!is_null($this->accessToken)) {
            $this->client->authenticate($this->accessToken);
        }
    }
}
