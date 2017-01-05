<?php

namespace JonathanTorres\MediumSdk\Tests;

use JonathanTorres\MediumSdk\Medium;
use PHPUnit_Framework_TestCase as PHPUnit;

class MediumIntegrationTest extends PHPUnit
{
    protected $medium;
    protected $authorId = '147563c42ee920bd60ef3e19d8d8e0001310828c192e0297e06ad991fad843b0d';
    protected $publicationId = 'b45573563f5a';

    protected function setUp()
    {
        $this->medium = new Medium(getenv('MEDIUM_TOKEN'));
    }

    public function testGetAuthenticatedUser()
    {
        $user = $this->medium->getAuthenticatedUser();

        $this->assertTrue(isset($user->data));
        $this->assertTrue(isset($user->data->id));
    }

    public function testGetUserPublications()
    {
        $publications = $this->medium->publications($this->authorId);

        $this->assertTrue(isset($publications->data));
    }

    public function testGetPublicationContributors()
    {
        $contributors = $this->medium->contributors($this->publicationId);

        $this->assertTrue(isset($contributors->data));
        $this->assertTrue(isset($contributors->data[0]->publicationId));
        $this->assertTrue(isset($contributors->data[0]->userId));
        $this->assertTrue(isset($contributors->data[0]->role));
    }

    public function testCreateUserPost()
    {
        $data = [
            'title' => 'Post created from Medium SDK Integration Test',
            'contentFormat' => 'html',
            'content' => 'This is an automated test.',
            'publishStatus' => 'draft',
        ];

        $post = $this->medium->createPost($this->authorId, $data);

        $this->assertTrue(isset($post->data));
        $this->assertTrue(isset($post->data->id));
        $this->assertTrue(isset($post->data->title));
        $this->assertTrue(isset($post->data->authorId));
        $this->assertTrue(isset($post->data->url));
        $this->assertTrue(isset($post->data->canonicalUrl));
        $this->assertTrue(isset($post->data->publishStatus));
        $this->assertTrue(isset($post->data->license));
        $this->assertTrue(isset($post->data->licenseUrl));
        $this->assertTrue(isset($post->data->tags));
    }

    public function testUploadImage()
    {
        $image = fopen(__DIR__ . '/zebra.png', 'r');
        $upload = $this->medium->uploadImage($image, 'my_zebra.png');

        $this->assertTrue(isset($upload->data));
        $this->assertTrue(isset($upload->data->url));
        $this->assertTrue(isset($upload->data->md5));
    }
}
