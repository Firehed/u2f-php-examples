<?php
declare(strict_types=1);

namespace Firehed\Webauthn\Endpoints;

use Firehed\API\Interfaces\EndpointInterface;
use Firehed\API\Traits;
use Firehed\Input\Containers\SafeInput;
use Firehed\InputObjects\{Any, Text};
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
    use Traits\Request\Post;


    private Server $server;

    public function __construct(Server $server)
    {
        $this->server = $server;
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
        $response = RegistrationResponse::fromDecodedJson($input->asArray());
        error_log(print_r($response, true));

        $regReq = $_SESSION['REGISTRATION_REQUEST'];
        error_log(print_r($regReq, true));

        $this->server->setRegisterRequest($regReq);

        $registration = $this->server->register($response);

        error_log(print_r($registration, true));
        // add reg to user's data
    }
}
