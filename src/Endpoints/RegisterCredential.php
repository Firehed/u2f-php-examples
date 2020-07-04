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
    WebAuthn\RegistrationResponse,
};

class RegisterCredential implements EndpointInterface
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
        return '/registerCredential';
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
        $response = RegistrationResponse::fromDecodedJson($input->asArray());

        $regReq = $_SESSION['REGISTRATION_REQUEST'];

        $this->server->setRegisterRequest($regReq);

        $registration = $this->server->register($response);

        $user->addRegistration($registration);
        $user->addRegistration($registration);
        $this->userStorage->save($user);

        return $this->textResponse('registered! hit back');
    }
}
