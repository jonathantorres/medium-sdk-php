<?php

namespace JonathanTorres\MediumSdk\Tests;

use JonathanTorres\MediumSdk\Medium;
use Mockery;
use PHPUnit\Framework\TestCase;
use StdClass;

class MediumTest extends TestCase
{
    protected $medium;
    protected $mediumClient;
    protected $authorizationCode = '1234567890';
    protected $credentials = [
        'client-id' => 'CLIENT-ID',
        'client-secret' => 'CLIENT-SECRET',
        'redirect-url' => 'http://someurl.com/callback',
        'state' => 'somesecret',
        'scopes' => 'scope1,scope2',
    ];

    protected function setUp(): void
    {
        $this->medium = new Medium();
        $this->mediumClient = Mockery::mock('JonathanTorres\MediumSdk\Client');
    }

    public function testConnectToApiInConstructor(): void
    {
        $medium = new Medium($this->credentials);

        $this->assertEquals('CLIENT-ID', $medium->getClientId());
        $this->assertEquals('CLIENT-SECRET', $medium->getClientSecret());
    }

    public function testConnectToApiWithConnectMethod(): void
    {
        $medium = new Medium();
        $medium->connect($this->credentials);

        $this->assertEquals('CLIENT-ID', $medium->getClientId());
        $this->assertEquals('CLIENT-SECRET', $medium->getClientSecret());
    }

    public function testConnectToApiInConstructorUsingSelfIssuedAccessToken(): void
    {
        $medium = new Medium('SELF-ISSUED-ACCESS-TOKEN');

        $this->assertNotNull($medium->getAccessToken());
        $this->assertEquals('SELF-ISSUED-ACCESS-TOKEN', $medium->getAccessToken());
    }

    public function testConnectToApiWithConnectMethodUsingSelfIssuedAccessToken(): void
    {
        $this->medium->connect('SELF-ISSUED-ACCESS-TOKEN');

        $this->assertNotNull($this->medium->getAccessToken());
        $this->assertEquals('SELF-ISSUED-ACCESS-TOKEN', $this->medium->getAccessToken());
    }

    public function testGetAuthenticationUrl(): void
    {
        $this->medium->connect($this->credentials);

        $expectedUrl = 'https://medium.com/m/oauth/authorize?client_id=CLIENT-ID&' .
                       'scope=scope1%2Cscope2&state=somesecret&response_type=code&' .
                       'redirect_uri=http%3A%2F%2Fsomeurl.com%2Fcallback';

        $authenticationUrl = $this->medium->getAuthenticationUrl();

        $this->assertEquals($expectedUrl, $authenticationUrl);
    }

    public function testAuthentication(): void
    {
        $this->authenticationMocks();

        $this->medium->connect($this->credentials);
        $this->medium->setClient($this->mediumClient);
        $this->medium->authenticate($this->authorizationCode);

        $this->assertEquals('ACCESS-TOKEN', $this->medium->getAccessToken());
    }

    public function testGetRefreshToken(): void
    {
        $this->mediumClient->shouldReceive('exchangeRefreshToken')->once()
                           ->with('1234567890', 'CLIENT-ID', 'CLIENT-SECRET')
                           ->andReturn('NEW-ACCESS-TOKEN');

        $refreshToken = '1234567890';
        $this->medium->connect($this->credentials);
        $this->medium->setClient($this->mediumClient);
        $accessToken = $this->medium->exchangeRefreshToken($refreshToken);

        $this->assertEquals('NEW-ACCESS-TOKEN', $accessToken);
    }

    public function testGetAuthenticatedUser(): void
    {
        $this->authenticationMocks();
        $this->mediumClient->shouldReceive('makeRequest')->once()
                           ->with('GET', 'me')->andReturn(new StdClass);

        $this->medium->connect($this->credentials);
        $this->medium->setClient($this->mediumClient);
        $this->medium->authenticate($this->authorizationCode);

        $user = $this->medium->getAuthenticatedUser();
        $this->assertNotNull($user);
    }

    public function testGetUserPublications(): void
    {
        $this->authenticationMocks();
        $this->mediumClient->shouldReceive('makeRequest')->once()
                           ->with('GET', 'users/12345/publications')->andReturn(new StdClass);

        $this->medium->connect($this->credentials);
        $this->medium->setClient($this->mediumClient);
        $this->medium->authenticate($this->authorizationCode);

        $publications = $this->medium->publications('12345');
        $this->assertNotNull($publications);
    }

    public function testGetPublicationContributors(): void
    {
        $this->authenticationMocks();
        $this->mediumClient->shouldReceive('makeRequest')->once()
                           ->with('GET', 'publications/12345/contributors')->andReturn(new StdClass);

        $this->medium->connect($this->credentials);
        $this->medium->setClient($this->mediumClient);
        $this->medium->authenticate($this->authorizationCode);

        $contributors = $this->medium->contributors('12345');
        $this->assertNotNull($contributors);
    }

    public function testCreateUserPost(): void
    {
        $postData = [
            'title' => 'Post title',
            'contentFormat' => 'html',
            'content' => 'This is my post content.',
            'publishStatus' => 'draft',
        ];

        $requestData['form_params'] = $postData;

        $this->authenticationMocks();
        $this->mediumClient->shouldReceive('makeRequest')->once()
                           ->with('POST', 'users/12345/posts', $requestData)->andReturn(new StdClass);

        $this->medium->connect($this->credentials);
        $this->medium->setClient($this->mediumClient);
        $this->medium->authenticate($this->authorizationCode);

        $post = $this->medium->createPost('12345', $postData);
        $this->assertNotNull($post);
    }

    public function testCreateUserPostUnderAPublication(): void
    {
        $postData = [
            'title' => 'Post title',
            'contentFormat' => 'html',
            'content' => 'This is my post content.',
            'publishStatus' => 'draft',
        ];

        $requestData['form_params'] = $postData;

        $this->authenticationMocks();
        $this->mediumClient->shouldReceive('makeRequest')->once()
                           ->with('POST', 'publications/12345/posts', $requestData)->andReturn(new StdClass);

        $this->medium->connect($this->credentials);
        $this->medium->setClient($this->mediumClient);
        $this->medium->authenticate($this->authorizationCode);

        $post = $this->medium->createPostUnderPublication('12345', $postData);
        $this->assertNotNull($post);
    }

    public function testUploadImage(): void
    {
        $requestData = [
            'multipart' => [
                [
                    'name' => 'image',
                    'filename' => 'myimage.jpg',
                    'contents' => 'imagedata',
                ],
            ]
        ];

        $this->authenticationMocks();
        $this->mediumClient->shouldReceive('makeRequest')->once()
                           ->with('POST', 'images', $requestData)->andReturn(new StdClass);

        $this->medium->connect($this->credentials);
        $this->medium->setClient($this->mediumClient);
        $this->medium->authenticate($this->authorizationCode);

        $image = $this->medium->uploadImage('imagedata', 'myimage.jpg');
        $this->assertNotNull($image);
    }

    private function authenticationMocks(): void
    {
        $tokens = new StdClass();
        $tokens->access_token = 'ACCESS-TOKEN';
        $tokens->refresh_token = 'REFRESH-TOKEN';
        $tokens->expires_at = time();

        $this->mediumClient->shouldReceive('requestTokens')->once()
                           ->with('1234567890', 'CLIENT-ID', 'CLIENT-SECRET', 'http://someurl.com/callback')
                           ->andReturn($tokens);
        $this->mediumClient->shouldReceive('authenticate')->once()
                           ->with('ACCESS-TOKEN')->andReturnNull();
    }
}
