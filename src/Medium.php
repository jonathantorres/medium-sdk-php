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
     * Initialize.
     *
     * @param string $accessToken
     *
     * @return void
     */
    public function __construct($accessToken = null)
    {
        if (!is_null($accessToken)) {
            $this->accessToken = $accessToken;
            $this->setUpApiClient();
        }
    }

    /**
     * Connect to the api using the access token.
     *
     * @param string $accessToken
     *
     * @return boolean
     */
    public function connect($accessToken)
    {
        $this->accessToken = $accessToken;
        $this->setUpApiClient();
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
     * Create a post.
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
     * Set up medium's api client.
     *
     * @return void
     */
    private function setUpApiClient()
    {
        $this->client = new Client($this->accessToken);
    }
}
