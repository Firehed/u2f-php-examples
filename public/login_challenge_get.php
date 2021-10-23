<?php

declare(strict_types=1);

$app = require dirname(__DIR__) . '/bootstrap.php';
$server = $app['server'];
$storage = $app['storage'];

$user = $storage->get($_SESSION['USER_NAME']);
assert($user !== null);

$registrations = $user->getRegistrations();

$signRequests = $server->generateSignRequests($registrations);

$_SESSION['SIGN_REQUESTS'] = $signRequests;

// WebAuthn expects a single challenge for all key handles, and the Server generates the requests accordingly.
$data = [
    'challenge' => $signRequests[0]->getChallenge(),
    'keyHandles' => array_map(function (Firehed\U2F\SignRequest $sr) {
        return $sr->getKeyHandleWeb();
    }, $signRequests),
];

header('HTTP/1.1 200 OK');
header('Content-type: application/json');
echo json_encode($data);
