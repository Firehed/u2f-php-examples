<?php declare(strict_types=1);

require_once 'vendor/autoload.php';

use Firehed\JWT\{
    Algorithm,
    KeyContainer,
    SessionHandler
};
use Firehed\Security\Secret;
use Firehed\U2F\Server;

// This configures sessions to use JWTs instead of persisting any data on the
// server. Your application's existing session handling should work fine. If
// you use JWT session storage, you absolutely must change the Secret below or
// the signed client-side data can be forged.
$keyContainer = new KeyContainer();
$keyContainer->addKey('20160303',
    Algorithm::HMAC_SHA_256(),
    new Secret('asdfklj209flwjaflksdnflk2ifnas'));
$sh = new SessionHandler($keyContainer);
ini_set('session.use_cookies', 'false');
session_set_cookie_params(
    $lifetime = 0,
    $path = '/',
    $domain = '',
    $secure = true,
    $httponly = true
);
session_set_save_handler($sh);

session_start();

ob_start();

// This will intentionally leak into the other files' scope; normally, you'd set this up in a dependency inversion container or config file
$server = (new Server())
//    ->setTrustedCAs(glob(__DIR__.'/vendor/firehed/u2f/CAcerts/*.pem'))
    ->disableCAVerification() // Don't verify CAs during demo
    ->setAppId('https://u2f.ericstern.com'); // This needs to be your site, and must be HTTPS

// This is a dumbed-down "load user by username" function
function get_user_data(string $user): array {
    $file = get_user_path($user);
    if (!file_exists($file)) {
        return [];
    }
    return unserialize(file_get_contents($file));
}
// This is a dumbed-down "save user" function
function write_user_data(string $user, $data) {
    file_put_contents(get_user_path($user), serialize($data));
}
function get_user_path(string $user): string {
    return __DIR__.'/users/'.basename($user).'.dat';
}
