<?php

declare(strict_types=1);

$app = require dirname(__DIR__) . '/bootstrap.php';
$server = $app['server'];

$challenge = $server->generateChallenge();
$_SESSION['REGISTRATION_CHALLENGE'] = $challenge;

header('HTTP/1.1 200 OK');
header('Content-type: application/json');
echo json_encode($challenge);
