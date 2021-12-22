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
// $input = json_decode($rawJson, true);

$response = Firehed\U2F\WebAuthn\Web\AuthenticatorAssertionResponse::parseJson($rawJson);
// $response = Firehed\U2F\WebAuthn\LoginResponse::fromDecodedJson($input);

$challenge = $_SESSION['LOGIN_CHALLENGE'];

$s2 = new Firehed\U2F\WebAuthn\RelyingPartyServer('http://localhost:8887');
$ret = $s2->login($response, $challenge, $user->getRegistrations());
// $registration = $server->validateLogin($challenge, $response, $user->getRegistrations());

// This keeps the token counters, etc, up to date.
$user->updateRegistration($registration);
$storage->save($user);

header('HTTP/1.1 200 OK');
echo 'token is good';
