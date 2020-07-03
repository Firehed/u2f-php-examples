<?php
declare(strict_types=1);

return [

    Firehed\Webauthn\Endpoints\GetRegistrationChallenge::class,
    Firehed\Webauthn\Endpoints\RegisterCredential::class,

    Firehed\U2F\Server::class => function () {
        $server = new Firehed\U2F\Server();
        $server->setAppId('localhost');
        $server->disableCAVerification();
        return $server;
    },

    Psr\Log\LoggerInterface::class => function () {
        $logger = new Monolog\Logger('http');
        $logger->pushHandler(
            new Monolog\Handler\StreamHandler('php://stdout'),
        );
        return $logger;
    },

];
