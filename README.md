Medium SDK for PHP
================
[![Build Status](https://travis-ci.org/jonathantorres/medium-sdk-php.svg)](https://travis-ci.org/jonathantorres/medium-sdk-php)
[![Version](https://img.shields.io/packagist/v/jonathantorres/medium-sdk.svg)](https://packagist.org/packages/jonathantorres/medium-sdk)

Open source SDK for integrating [Medium](https://medium.com)'s OAuth2 API into your PHP application. Please note that Medium's API is still on an early stage and this implementation is not final. Breaking changes will happen. This SDK is unofficial. Medium's API documentation can be found [here](https://github.com/Medium/medium-api-docs).

## Installation
```bash
composer require jonathantorres/medium-sdk
```

## Authentication
Initialize the SDK with your client credentials:
```php
    use JonathanTorres\MediumSdk\Medium;

    $credentials = [
        'client-id' => 'CLIENT-ID',
        'client-secret' => 'CLIENT-SECRET',
        'redirect-url' => 'http://example.com/callback',
        'state' => 'somesecret',
        'scopes' => 'scope1,scope2',
    ];

    $medium = new Medium($credentials);
```

You can also use the `connect` method.
```php
    use JonathanTorres\MediumSdk\Medium;

    $medium = new Medium();
    $medium->connect($credentials);
```

#### Browser-based authentication
Request the authentication url, this url will take the user to medium's authentication page. If successfull, it will return an authorization code.
```php
    $authUrl = $medium->getAuthenticationUrl();

    <a href="<?php echo $authUrl; ?>">Authenticate with Medium</a>
```

Grab the authorization code from the url and use the `authenticate` method to be able to make requests to the API. Now you should be able to start making requests.
```php
    $authorizationCode = $_GET['code'];
    $medium->authenticate($authorizationCode);
```

#### Generate a new access token
Access tokens are valid for 60 days. Once it expires, you can request a new access token using your refresh token. Refresh tokens do not expire. You can request a new access token using your refresh token.
```php
    $accessToken = $medium->exchangeRefreshToken($refreshToken);
    $medium->setAccessToken($accessToken);
```

#### Authenticating with a self-issued access token
Medium recommends to use browser-based authentication, but you can also make requests to the API using a self-issued access token generated from your Medium [settings page](https://medium.com/me/settings). These types of tokens never expire. Once you have it you can authenticate using this access token.
```php
    $medium = new Medium('SELF-ISSUED-ACCESS-TOKEN');
```

You can also use the `connect` method.
```php
    $medium = new Medium();
    $medium->connect('SELF-ISSUED-ACCESS-TOKEN');
```

Now you should be ready to start making requests to the API using your self issued access token.

## Users
#### Get the authenticated user details.
This will return an object with the user's details:
```php
    $user = $medium->getAuthenticatedUser();

    echo 'Authenticated user name is: ' . $user->data->name;
```

## Publications
#### List the specified user publications
This will return an array of objects that represent a publication that the specified user is related to in some way.
```php
    $publications = $medium->publications($userId)->data;

    foreach($publications as $publication) {
        echo 'Publication name: ' . $publication->name;
    }
```

#### List the contributors for the specified publication
This will return an array of users that are allowed to publish under the specified publication.
```php
    $contributors = $medium->contributors($publicationId)->data;

    foreach($contributors as $contributor) {
        echo 'User ' . $contributor->userId . ' is an ' . $contributor->role . ' on ' . $contributor->publicationId;
    }
```

## Posts
#### Creating a post
This will create a post on the authenticated user's profile. Provide the id of the authenticated user and the post data. This will return an object with the created post's details.
```php
    $user = $medium->getAuthenticatedUser();
    $data = [
        'title' => 'Post title',
        'contentFormat' => 'html',
        'content' => 'This is my post content.',
        'publishStatus' => 'draft',
    ];

    $post = $medium->createPost($user->data->id, $data);

    echo 'Created post: ' . $post->data->title;
```

#### Creating a post under a publication
This will create a post on the authenticated user's profile but also associate it with a publication. Provide the same data as creating a post. The response will also be the same with the exception of adding the `publicationId` field.
```php
    $data = [
        'title' => 'Post title',
        'contentFormat' => 'html',
        'content' => 'This is my post content.',
        'publishStatus' => 'draft',
    ];

    $post = $medium->createPostUnderPublication($publicationId, $data);

    echo 'Created post: ' . $post->data->title . ' under the publication ' . $post->data->publicationId;
```

## Images
#### Uploading an image.
Provide an image resource, the image name and the extension. This will return an object with the uploaded image's data.
```php
    $imageResource = fopen('path/to/your/image', 'r');
    $image = $medium->uploadImage($imageResource, 'image-filename.jpg');

    echo 'Uploaded image ' . $image->data->url . ' succesfully.';
```

## Running the examples
After cloning your repo:
```bash
git clone git@github.com:jonathantorres/medium-sdk-php.git
```

Add your API credentials on `examples/credentials.php`
```php
    $credentials = [
        'client-id' => 'YOUR-CLIENT-ID',
        'client-secret' => 'YOUR-CLIENT-SECRET',
        'redirect-url' => 'http://localhost:8888/callback.php',
        'state' => 'secret',
        'scopes' => 'basicProfile,publishPost,listPublications',
    ];
```

Start the included php server on the examples folder:
```bash
cd medium-sdk-php/examples && php -S localhost:8888
```

## Run tests
Tests are written with [PHPUnit](http://phpunit.de).

After cloning your repo:
```bash
git clone git@github.com:jonathantorres/medium-sdk-php.git
```

Generate a self-issued access token from your Medium [settings page](https://medium.com/me/settings). You need this to run the integration tests. Then, just run `composer test` on the project's root directory:
```bash
cd medium-sdk-php
export MEDIUM_TOKEN=YOUR_ACCESS_TOKEN; composer test
```

## To-do's
- Laravel Service Provider.
- Symfony Bundle.

### Changelog
Please see [CHANGELOG](CHANGELOG.md) for more information.

### Contributing
Please see [CONTRIBUTING](CONTRIBUTING.md) for more details.

### License
Licensed under the MIT license. Please see [License file](LICENSE.md) for more information.
