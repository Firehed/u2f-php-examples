<?php

declare(strict_types=1);

$app = require dirname(__DIR__) . '/bootstrap.php';
$server = $app['server'];

$registerRequest = $server->generateRegisterRequest();
$_SESSION['REGISTRATION_REQUEST'] = $registerRequest;

header('HTTP/1.1 200 OK');
header('Content-type: application/json');
echo json_encode($registerRequest->getChallenge());
