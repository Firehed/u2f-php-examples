<?php

declare(strict_types=1);

$app = require dirname(__DIR__) . '/bootstrap.php';
$server = $app['server'];
$storage = $app['storage'];

$user = $storage->get($_SESSION['USER_NAME']);
assert($user !== null);

// Get and decode JSON from POST request
$rawJson = file_get_contents('php://input');
assert(is_string($rawJson));
$input = json_decode($rawJson, true);

$response = Firehed\U2F\WebAuthn\LoginResponse::fromDecodedJson($input);

$signRequests = $_SESSION['SIGN_REQUESTS'];

$server->setRegistrations($user->getRegistrations());
$server->setSignRequests($signRequests);

$registration = $server->authenticate($response);

// This keeps the token counters, etc, up to date.
$user->updateRegistration($registration);
$storage->save($user);

header('HTTP/1.1 200 OK');
echo 'token is good';
