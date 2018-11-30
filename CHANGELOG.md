# Changelog

#### v0.3.2 `2018-11-29`
- `Fixed`
    - Added expired date in unix timestamp for access tokens. See [#11](https://github.com/jonathantorres/medium-sdk-php/pull/11)

#### v0.3.1 `2018-03-22`
- `Fixed`
    - Convert Guzzle's `GuzzleHttp\Psr7\Stream` to a string so it can be decoded. See [#9](https://github.com/jonathantorres/medium-sdk-php/pull/9)

#### v0.3.0 `2017-01-07`
- `Added`
    - Method `getRefreshToken();` to get the refresh token after authentication. See [#5](https://github.com/jonathantorres/medium-sdk-php/pull/5)

#### v0.2.0 `2015-12-07`
- `Added`
    - Methods `publications();` and `contributors();` to get publications and publication contributors.
    - Method `createPostUnderPublication();` to create a post under a specific publication.
    - Examples to explain functionality.
    - Updates on docs.

#### v0.1.0 `2015-10-19`
- First release.
