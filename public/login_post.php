<?php

declare(strict_types=1);

$app = require dirname(__DIR__) . '/bootstrap.php';
$storage = $app['storage'];

assert(isset($_POST['username']));
assert(isset($_POST['password']));

$user = $storage->get($_POST['username']);

if (!$user) {
    header('HTTP/1.1 403 Unauthorized');
    echo 'Not registered';
    return;
}

$isValidPassword = $user->isPasswordCorrect($_POST['password']);

if (!$isValidPassword) {
    header('HTTP/1.1 403 Unauthorized');
    echo 'Incorrect passord';
    return;
}

$_SESSION['USER_NAME'] = $user->getName();

header('HTTP/1.1 200 OK');
echo 'Login ok. Go back to add a token';
