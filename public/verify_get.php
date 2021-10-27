<?php

declare(strict_types=1);

$app = require dirname(__DIR__) . '/bootstrap.php';
$server = $app['server'];
$storage = $app['storage'];

$user = $storage->get($_SESSION['USER_NAME']);
assert($user !== null);

$registrations = $user->getRegistrations();
if (count($registrations) === 0) {
    header('400 Bad Request');
    echo 'No tokens have been registered yet.';
    return;
}

$challenge = $server->generateChallenge();
$_SESSION['LOGIN_CHALLENGE'] = $challenge;

// WebAuthn expects a single challenge for all key handles, and the Server generates the requests accordingly.
$data = [
    'challenge' => $challenge,
    'keyHandles' => array_map(function (Firehed\U2F\RegistrationInterface $reg) {
        return $reg->getKeyHandleWeb();
    }, $registrations),
];

header('HTTP/1.1 200 OK');
header('Content-type: application/json');
echo json_encode($data);
