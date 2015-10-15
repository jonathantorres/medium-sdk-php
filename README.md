Medium SDK for PHP
================
Open source SDK for integrating [Medium](https://medium.com)'s OAuth2 API into your PHP application. Please note that Medium's API is still on an early stage and this implementation is not final. Breaking changes will happen.

#### Installation
```bash
composer require jonathantorres/medium-sdk
```

#### Usage
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

Grab the authorization code from the url and use the `authenticate` method to be able to make requests to the API.

```php
    $authorizationCode = $_GET['code'];
    $medium->authenticate($authorizationCode);
```


#### Changelog
Please see [CHANGELOG](CHANGELOG.md) for more information.

#### Running Tests
``` bash
$ composer test
```

#### Contributing
Please see [CONTRIBUTING](CONTRIBUTING.md) for more details.

#### License
This library is licensed under the MIT license. Please see [License file](LICENSE.md) for more information.
