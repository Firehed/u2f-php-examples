# U2F-PHP Examples

This is a simple demo application that goes along with [firehed/u2f](https://github.com/Firehed/u2f-php).

## Live Demo
[https://u2f.ericstern.com](https://u2f.ericstern.com)

## Requirements

Since this demo is showing off authentication with the `fido-u2f` protocol of WebAuthn, you must physically have a FIDO U2F Token.
You can get one [from Amazon](http://www.amazon.com/s/ref=nb_sb_noss?url=search-alias%3Daps&field-keywords=u2f) for as little as $6.
If you have a YubiKey, that will work.

## What it shows

The pages linked from [`index.html`](public/index.html) individually show what would happen during user registration and adding a token to a user's account.
You should step through them in order, preferably with your browser's web inspector open.

The first two pages are a standard user registration flow, and are mechanically necessary for the demo but don't add much.
`add_token.html` and the matching PHP files demonstrate generating a challenge to send to the user, verifying their signed response, and storing the registration.
`verify.html` and the matching PHP files demonstrate generating challenges for the user's registered devices and verifying their signed response to update their session to two-factor level.

It's a very 2004-era "upload with FTP and you're done" approach, so that you can focus on understanding the pairs of "generate request"/"process response" endpoints.

## What it doesn't show

This is intended to be a very simple example, doing the least amount possible to demonstrate how to use the U2F library.
That means it intentionally leaves out best practices you would expect in a larger application: routers, models, DBALs, dependency inversion containers, etc.

In a real application, each of the php files would be some sort of standard controller, API endpoint, etc.

## Notes

If you're trying to run the example locally, you must do a few things:

1. `composer install`
2. `php -S 0.0.0.0:8000 -t public/`
3. Visit `http://localhost:8080` in any browser that [supports WebAuthn](https://developer.mozilla.org/en-US/docs/Web/API/Web_Authentication_API#browser_compatibility)

Note that the Web Authentition APIs *only* work in "secure contexts", which means that to run it anywhere other than `localhost`, you *must* use HTTPS.
