<?php

declare(strict_types=1);

chdir(__DIR__);

require 'vendor/autoload.php';

session_start();

$server = new Firehed\U2F\Server();
$server->disableCAVerification(); // Skip verification in demo
// $server->setAppId('http://localhost:8887');
$server->setAppId('localhost');

return [
    'server' => $server,
];
