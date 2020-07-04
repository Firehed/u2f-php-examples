<?php
declare(strict_types=1);

namespace Firehed\Webauthn\Endpoints;

use Firehed\API\Interfaces\EndpointInterface;
use Firehed\API\Traits;
use Firehed\U2F\{SignRequest, Server};
use Firehed\Input\Containers\SafeInput;
use Firehed\InputObjects;
use Firehed\Webauthn\UserStorage;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class GetLoginChallenge implements EndpointInterface
{
    use Traits\Request\Get;
    use Traits\ResponseBuilder;

    private Server $server;
    private UserStorage $userStorage;

    public function __construct(Server $server, UserStorage $userStorage)
    {
        $this->server = $server;
        $this->userStorage = $userStorage;
    }

    public function getUri(): string
    {
        return '/getLoginChallenge';
    }

    public function getRequiredInputs(): array
    {
        return [];
    }

    public function getOptionalInputs(): array
    {
        return [];
    }

    public function execute(SafeInput $input): ResponseInterface
    {
        $user = $this->userStorage->get($_SESSION['USER_NAME']);
        assert($user !== null);

        $registrations = $user->getRegistrations();

        $signRequests = $this->server->generateSignRequests($registrations);
        $_SESSION['SIGN_REQUESTS'] = $signRequests;

        // WebAuthn expects a single challenge for all key handles, and the Server generates the requests accordingly.
        return $this->jsonResponse([
            'challenge' => $signRequests[0]->getChallenge(),
            'keyHandles' => array_map(function (SignRequest $sr) {
                return $sr->getKeyHandleWeb();
            }, $signRequests),
        ]);
    }
}
