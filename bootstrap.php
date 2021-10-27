<?php

declare(strict_types=1);

chdir(__DIR__);

require 'vendor/autoload.php';

session_start();

// HOSTNAME should be set to the domain name you're running on; it's part of
// the security protocol. If set to `localhost`, browsers will treat it as
// a secure context even when not running over https; anything else will cause
// the WebAuthn APIs (navigator.credentials) to fail.
$hostname = getenv('HOSTNAME') ?: 'localhost';

$server = new Firehed\U2F\Server($hostname);
$server->disableCAVerification(); // Skip verification in demo

return [
    'server' => $server,
    'storage' => new Firehed\Webauthn\UserStorage(),
];
