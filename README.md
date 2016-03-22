# U2F-PHP Examples

This is a simple demo application that goes along with [firehed/u2f](https://github.com/Firehed/u2f-php).

## Live Demo
[https://u2f.ericstern.com]()

## Requirements

Since this demo is showing off authentication with the U2F protocol, you must physically have a FIDO U2F Token. You can get one [from Amazon](http://www.amazon.com/s/ref=nb_sb_noss?url=search-alias%3Daps&field-keywords=u2f) for as little as $6.

## What it shows

The forms in [`index.html`](public/index.html) individually show what would happen during user registration and adding a token to a user's account. Each one is powered by an AJAX handler to shuffle data between the client and server (see [`site.js`](public/site.js))

Each of the PHP files in `public/` power one of those AJAX endpoints, so that you can see the general inputs and outputs of each page. 

It's a very 2004-era "upload with FTP and you're done" approach, so that you can focus on understanding the pairs of "generate request"/"process response" endpoints.

## What it doesn't show

This is intended to be a very simple example, doing the least amount possible to demonstrate how to use the U2F library. That means it intentionally leaves out best practices you would expect in a larger application: routers, models, DBALs, dependency inversion containers, etc.

In a real application, each of the php files would be some sort of standard controller, API endpoint, etc.

## Notes

If you're trying to run the example locally, you must do three things:

1. `composer install`
2. Configure HTTPS
3. Set up a webserver to serve out of the `public/` directory

Why HTTPS? [Because browsers will reject HTTP](https://fidoalliance.org/specs/fido-u2f-v1.0-nfc-bt-amendment-20150514/fido-appid-and-facets.html#appid-example-1). You need HTTPS in production for your authentication to be remotely meaningful anyway.

This means you can't just use the built-in PHP webserver. Sorry.

To avoid having to screw around with setting it up locally in a development environment, just use the demo above and watch traffic in the browser's development tools.
