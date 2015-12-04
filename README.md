Medium SDK for PHP
================
Open source SDK for integrating [Medium](https://medium.com)'s OAuth2 API into your PHP application. Please note that Medium's API is still on an early stage and this implementation is not final. Breaking changes will happen. This SDK is unofficial. Medium's API documentation can be found [here](https://github.com/Medium/medium-api-docs).

## Installation
```bash
composer require jonathantorres/medium-sdk
```

## Usage
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

Request the authentication url, this url will take the user to medium's authentication page. If successfull, it will return an authorization code.
```php
    $medium->getAuthenticationUrl();
```

Grab the authorization code from the url and use the `authenticate` method to be able to make requests to the API. Now you should be able to start making requests.
```php
    $authorizationCode = $_GET['code'];
    $medium->authenticate($authorizationCode);
```

## Users
#### Get the authenticated user details.
This will return an object with the user's details:
```php
    $user = $medium->getAuthenticatedUser();

    echo 'Authenticated user name is: ' . $user->name;
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

### Posts
Creates a post on the authenticated user's profile.
```php
    $user = $medium->getAuthenticatedUser();
    $data = [
        'title' => 'Post title',
        'contentFormat' => 'html',
        'content' => 'This is my post content.',
        'publishStatus' => 'draft',
    ];

    $post = $medium->createPost($user->data->id, $data);
```

Provide the id of the authenticated user and the post data. This will return an object with the created post's details.
```php
    $post->data->id;
    $post->data->title;
    $post->data->authorId;
    $post->data->tags;
    $post->data->url;
    $post->data->canonicalUrl;
    $post->data->publishStatus;
    $post->data->publishedAt;
    $post->data->license;
    $post->data->licenseUrl;
```

### Images
Uploading an image.
```php
    $imageResource = fopen('path/to/your/image', 'r');
    $image = $medium->uploadImage($image, 'image-filename.jpg');
```

Provide an image resource and the name and extension of the image. This will return an object with the uploaded image's data.
```php
    $image->data->url;
    $image->data->md5;
```

### Authenticating with an self-issued access token
I would recommend to use the browser-based authentication, but you can also make requests to the API using a self-issued access token generated from your Medium [settings page](https://medium.com/me/settings). Once you have it you can authenticate using this access token.
```php
    $medium = new Medium('SELF-ISSUED-ACCESS-TOKEN');
```

You can also use the `connect` method.
```php
    $medium = new Medium();
    $medium->connect('SELF-ISSUED-ACCESS-TOKEN');
```

Now you should be ready to start making requests to the API using your generated access token.

### Generate a new access token
Access tokens are valid for 60 days. Once it expires, you can request a new access token using your refresh token. Refresh tokens do not expire. You can request a new access token using your refresh token.
```php
    $accessToken = $medium->exchangeRefreshToken($refreshToken);
    $medium->setAccessToken($accessToken);
```

### To-do's
- Laravel Service Provider.
- Symfony Bundle.
- Integration tests (WIP)
- Examples.

### Changelog
Please see [CHANGELOG](CHANGELOG.md) for more information.

### Contributing
Please see [CONTRIBUTING](CONTRIBUTING.md) for more details.

### License
Licensed under the MIT license. Please see [License file](LICENSE.md) for more information.
