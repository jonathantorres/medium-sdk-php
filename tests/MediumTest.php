<?php

namespace JonathanTorres\MediumSdk\Tests;

use JonathanTorres\MediumSdk\Medium;
use Mockery;
use PHPUnit_Framework_TestCase as PHPUnit;
use StdClass;

class MediumTest extends PHPUnit
{
    protected $medium;
    protected $mediumClient;

    protected function setUp()
    {
        $this->medium = new Medium();
        $this->mediumClient = Mockery::mock('JonathanTorres\MediumSdk\Client');
    }

    public function testConnectToApiInConstructor()
    {
        $credentials = [
            'client-id' => 'CLIENT-ID',
            'client-secret' => 'CLIENT-SECRET',
            'redirect-url' => 'http://someurl.com/callback',
            'state' => 'somesecret',
            'scopes' => 'scope1, scope2',
        ];

        $medium = new Medium($credentials);

        $this->assertEquals('CLIENT-ID', $medium->getClientId());
        $this->assertEquals('CLIENT-SECRET', $medium->getClientSecret());
    }

    public function testConnectToApiWithConnectMethod()
    {
        $credentials = [
            'client-id' => 'CLIENT-ID',
            'client-secret' => 'CLIENT-SECRET',
            'redirect-url' => 'http://someurl.com/callback',
            'state' => 'somesecret',
            'scopes' => 'scope1, scope2',
        ];

        $medium = new Medium();
        $medium->connect($credentials);

        $this->assertEquals('CLIENT-ID', $medium->getClientId());
        $this->assertEquals('CLIENT-SECRET', $medium->getClientSecret());
    }

    public function testConnectToApiInConstructorUsingSelfIssuedAccessToken()
    {
        $medium = new Medium('SELF-ISSUED-ACCESS-TOKEN');

        $this->assertNotNull($medium->getAccessToken());
        $this->assertEquals('SELF-ISSUED-ACCESS-TOKEN', $medium->getAccessToken());
    }

    public function testConnectToApiWithConnectMethodUsingSelfIssuedAccessToken()
    {
        $this->medium->connect('SELF-ISSUED-ACCESS-TOKEN');

        $this->assertNotNull($this->medium->getAccessToken());
        $this->assertEquals('SELF-ISSUED-ACCESS-TOKEN', $this->medium->getAccessToken());
    }

    public function testGetAuthenticationUrl()
    {
        $credentials = [
            'client-id' => 'CLIENT-ID',
            'client-secret' => 'CLIENT-SECRET',
            'redirect-url' => 'http://someurl.com/callback',
            'state' => 'somesecret',
            'scopes' => 'scope1,scope2',
        ];

        $medium = new Medium();
        $medium->connect($credentials);

        $expectedUrl = 'https://medium.com/m/oauth/authorize?client_id=CLIENT-ID&' .
                       'scope=scope1%2Cscope2&state=somesecret&response_type=code&' .
                       'redirect_uri=http%3A%2F%2Fsomeurl.com%2Fcallback';
        $authenticationUrl = $medium->getAuthenticationUrl();

        $this->assertEquals($expectedUrl, $authenticationUrl);
    }

    public function testAuthentication()
    {
        $credentials = [
            'client-id' => 'CLIENT-ID',
            'client-secret' => 'CLIENT-SECRET',
            'redirect-url' => 'http://someurl.com/callback',
            'state' => 'somesecret',
            'scopes' => 'scope1,scope2',
        ];

        $this->mediumClient->shouldReceive('requestAccessToken')->once()
                           ->with('1234567890', 'CLIENT-ID', 'CLIENT-SECRET', 'http://someurl.com/callback')
                           ->andReturn('ACCESS-TOKEN');
        $this->mediumClient->shouldReceive('authenticate')->once()
                           ->with('ACCESS-TOKEN')->andReturnNull();

        $authorizationCode = '1234567890';
        $medium = new Medium();
        $medium->connect($credentials);
        $medium->setClient($this->mediumClient);
        $medium->authenticate($authorizationCode);

        $this->assertEquals('ACCESS-TOKEN', $medium->getAccessToken());
    }

    public function testGetRefreshToken()
    {
        $credentials = [
            'client-id' => 'CLIENT-ID',
            'client-secret' => 'CLIENT-SECRET',
            'redirect-url' => 'http://someurl.com/callback',
            'state' => 'somesecret',
            'scopes' => 'scope1,scope2',
        ];

        $this->mediumClient->shouldReceive('exchangeRefreshToken')->once()
                           ->with('1234567890', 'CLIENT-ID', 'CLIENT-SECRET')
                           ->andReturn('NEW-ACCESS-TOKEN');

        $refreshToken = '1234567890';
        $medium = new Medium();
        $medium->connect($credentials);
        $medium->setClient($this->mediumClient);
        $accessToken = $medium->exchangeRefreshToken($refreshToken);

        $this->assertEquals('NEW-ACCESS-TOKEN', $accessToken);
    }

    public function testGetAuthenticatedUser()
    {
        $credentials = [
            'client-id' => 'CLIENT-ID',
            'client-secret' => 'CLIENT-SECRET',
            'redirect-url' => 'http://someurl.com/callback',
            'state' => 'somesecret',
            'scopes' => 'scope1,scope2',
        ];

        $this->mediumClient->shouldReceive('requestAccessToken')->once()
                           ->with('1234567890', 'CLIENT-ID', 'CLIENT-SECRET', 'http://someurl.com/callback')
                           ->andReturn('ACCESS-TOKEN');
        $this->mediumClient->shouldReceive('authenticate')->once()
                           ->with('ACCESS-TOKEN')->andReturnNull();
        $this->mediumClient->shouldReceive('makeRequest')->once()
                           ->with('GET', 'me')->andReturn(new StdClass);

        $authorizationCode = '1234567890';
        $this->medium->connect($credentials);
        $this->medium->setClient($this->mediumClient);
        $this->medium->authenticate($authorizationCode);

        $user = $this->medium->getAuthenticatedUser();
        $this->assertNotNull($user);
    }

    public function testCreateUserPost()
    {
        $credentials = [
            'client-id' => 'CLIENT-ID',
            'client-secret' => 'CLIENT-SECRET',
            'redirect-url' => 'http://someurl.com/callback',
            'state' => 'somesecret',
            'scopes' => 'scope1,scope2',
        ];

        $postData = [
            'title' => 'Post title',
            'contentFormat' => 'html',
            'content' => 'This is my post content.',
            'publishStatus' => 'draft',
        ];

        $requestData['form_params'] = $postData;

        $this->mediumClient->shouldReceive('requestAccessToken')->once()
                           ->with('1234567890', 'CLIENT-ID', 'CLIENT-SECRET', 'http://someurl.com/callback')
                           ->andReturn('ACCESS-TOKEN');
        $this->mediumClient->shouldReceive('authenticate')->once()
                           ->with('ACCESS-TOKEN')->andReturnNull();
        $this->mediumClient->shouldReceive('makeRequest')->once()
                           ->with('POST', 'users/12345/posts', $requestData)->andReturn(new StdClass);

        $authorizationCode = '1234567890';
        $this->medium->connect($credentials);
        $this->medium->setClient($this->mediumClient);
        $this->medium->authenticate($authorizationCode);

        $post = $this->medium->createPost('12345', $postData);
        $this->assertNotNull($post);
    }

    public function testUploadImage()
    {
        $credentials = [
            'client-id' => 'CLIENT-ID',
            'client-secret' => 'CLIENT-SECRET',
            'redirect-url' => 'http://someurl.com/callback',
            'state' => 'somesecret',
            'scopes' => 'scope1,scope2',
        ];

        $requestData = [
            'multipart' => [
                [
                    'name' => 'image',
                    'filename' => 'myimage.jpg',
                    'contents' => 'imagedata',
                ],
            ]
        ];

        $this->mediumClient->shouldReceive('requestAccessToken')->once()
                           ->with('1234567890', 'CLIENT-ID', 'CLIENT-SECRET', 'http://someurl.com/callback')
                           ->andReturn('ACCESS-TOKEN');
        $this->mediumClient->shouldReceive('authenticate')->once()
                           ->with('ACCESS-TOKEN')->andReturnNull();
        $this->mediumClient->shouldReceive('makeRequest')->once()
                           ->with('POST', 'images', $requestData)->andReturn(new StdClass);

        $authorizationCode = '1234567890';
        $this->medium->connect($credentials);
        $this->medium->setClient($this->mediumClient);
        $this->medium->authenticate($authorizationCode);

        $image = $this->medium->uploadImage('imagedata', 'myimage.jpg');
        $this->assertNotNull($image);
    }
}
