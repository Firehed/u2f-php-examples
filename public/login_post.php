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

// In a normal login flow, this is where you'd check for a user having 2FA
// enabled (e.g. checking for one or more registrations associated with the
// user) and, if so, force them directly into the second factor flow shown in
// use_token.html.
//
// You would, of course, also want to use a proper session auth storage
// mechanism. This is intentionally basic to make it easy to follow.

$_SESSION['USER_NAME'] = $user->getName();

header('HTTP/1.1 200 OK');
echo 'Login ok. Go back to add a token';
