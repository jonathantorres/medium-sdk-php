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
        $medium = new Medium('ACCESS-TOKEN');

        $this->assertNotNull($medium->getAccessToken());
        $this->assertEquals('ACCESS-TOKEN', $medium->getAccessToken());
    }

    public function testConnectToApiWithConnectMethod()
    {
        $this->medium->connect('ACCESS-TOKEN');

        $this->assertNotNull($this->medium->getAccessToken());
        $this->assertEquals('ACCESS-TOKEN', $this->medium->getAccessToken());
    }

    public function testGetAuthenticatedUser()
    {
        $this->mediumClient->shouldReceive('makeRequest')->once()
                           ->with('GET', 'me')->andReturn(new StdClass);
        $this->medium->setClient($this->mediumClient);

        $user = $this->medium->getAuthenticatedUser();
        $this->assertNotNull($user);
    }

    public function testCreateUserPost()
    {
        $postData = [
            'title' => 'Post title',
            'contentFormat' => 'html',
            'content' => 'This is my post content.',
            'publishStatus' => 'draft',
        ];

        $requestData['form_params'] = $postData;

        $this->mediumClient->shouldReceive('makeRequest')->once()
                           ->with('POST', 'users/12345/posts', $requestData)->andReturn(new StdClass);
        $this->medium->setClient($this->mediumClient);

        $post = $this->medium->createPost('12345', $postData);
        $this->assertNotNull($post);
    }

    public function testUploadImage()
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

        $this->mediumClient->shouldReceive('makeRequest')->once()
                           ->with('POST', 'images', $requestData)->andReturn(new StdClass);
        $this->medium->setClient($this->mediumClient);

        $image = $this->medium->uploadImage('imagedata', 'myimage.jpg');
        $this->assertNotNull($image);
    }
}
