<?php

declare(strict_types=1);

$app = require dirname(__DIR__) . '/bootstrap.php';
$storage = $app['storage'];

assert(isset($_POST['username']));
assert(isset($_POST['password']));

$user = $storage->get($_POST['username']);
if ($user) {
    header('HTTP/1.1 409 Conflict');
    echo "Already registered. Go back and use your user";
    return;
}

$user = new Firehed\Webauthn\User();
$user->setName($_POST['username']);
$user->setPassword($_POST['password']);

$storage->save($user);

header('HTTP/1.1 200 OK');
echo 'Registration succeeded. Go back to log in';
