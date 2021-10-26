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

$response = Firehed\U2F\WebAuthn\RegistrationResponse::fromDecodedJson($input);
$regReq = $_SESSION['REGISTRATION_REQUEST'];

$server->setRegisterRequest($regReq);

$registration = $server->register($response);

$user->addRegistration($registration);
$storage->save($user);

header('HTTP/1.1 200 OK');
echo 'Token registered successfully. Go back to use it.';
