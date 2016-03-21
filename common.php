<?php declare(strict_types=1);

require_once 'vendor/autoload.php';

use Firehed\JWT\{
    Algorithm,
    KeyContainer,
    SessionHandler
};
use Firehed\Security\Secret;
use Firehed\U2F\Server;

// This is for session storage
$keyContainer = new KeyContainer();
$keyContainer->addKey('20160303', Algorithm::HMAC_SHA_256(), new Secret('asdfklj209flwjaflksdnflk2ifnas'));
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

// This will intentionally leak into the other files' scope; normally, you'd set this up in a dependency inversion container or config file
$server = (new Server())
    ->setTrustedCAs(glob(__DIR__.'/vendor/firehed/u2f/CAcerts/*.pem'))
    ->setAppId('https://u2f.ericstern.com'); // This needs to be your site, and must be HTTPS

// This is a dumbed-down "load user by username" function
function get_user_data(string $user): array {
    $user = basename($user);
    if (!file_exists("users/$user.dat")) {
        return [];
    }
    return unserialize(file_get_contents("users/$user.dat"));
}
// This is a dumbed-down "save user" function
function write_user_data(string $user, $data) {
    $user = basename($user);
    file_put_contents("users/$user.dat", serialize($data));
}
