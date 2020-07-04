<?php
declare(strict_types=1);

namespace Firehed\Webauthn\Endpoints;

use Firehed\API\Interfaces\EndpointInterface;
use Firehed\API\Traits;
use Firehed\Input\Containers\SafeInput;
use Firehed\InputObjects\{Any, Text};
use Firehed\Webauthn\UserStorage;
use Psr\Http\Message\ResponseInterface;
use Throwable;

use Firehed\U2F\{
    Server,
    WebAuthn\LoginResponse,
};

class ValidateCredential implements EndpointInterface
{
    // use Input\NoOptional;
    // use Input\NoRequired;
    use Traits\ResponseBuilder;
    use Traits\Request\Post;

    private Server $server;
    private UserStorage $userStorage;

    public function __construct(Server $server, UserStorage $userStorage)
    {
        $this->server = $server;
        $this->userStorage = $userStorage;
    }

    public function getUri(): string
    {
        return '/validateCredential';
    }

    public function getRequiredInputs(): array
    {
        return [
            'rawId' => new Any(),
            'type' => new Text(),
            'response' => new Any(),
        ];
    }

    public function getOptionalInputs(): array
    {
        return [];
    }

    public function execute(SafeInput $input): ResponseInterface
    {
        $user = $this->userStorage->get($_SESSION['USER_NAME']);
        assert($user !== null);
        // doot doot
        $response = LoginResponse::fromDecodedJson($input->asArray());

        $signRequests = $_SESSION['SIGN_REQUESTS'];

        $this->server->setRegistrations($user->getRegistrations());
        $this->server->setSignRequests($signRequests);

        $registration = $this->server->authenticate($response);

        $user->updateRegistration($registration);
        $this->userStorage->save($user);

        error_log(print_r($registration, true));

        return $this->textResponse('all good?');
    }
}
