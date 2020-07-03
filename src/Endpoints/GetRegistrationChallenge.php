<?php
declare(strict_types=1);

namespace Firehed\Webauthn\Endpoints;

use Firehed\API\Interfaces\EndpointInterface;
use Firehed\API\Traits;
use Firehed\U2F\Server;
use Firehed\Input\Containers\SafeInput;
use Firehed\InputObjects;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class GetRegistrationChallenge implements EndpointInterface
{
    // use Input\NoOptional;
    // use Input\NoRequired;
    use Traits\Request\Get;
    use Traits\ResponseBuilder;

    private Server $server;

    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    public function getUri(): string
    {
        return '/getRegistrationChallenge';
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
        $rr = $this->server->generateRegisterRequest();

        // throw in session
        $_SESSION['REGISTRATION_REQUEST'] = $rr;

        return $this->jsonResponse($rr->getChallenge());
    }
}
