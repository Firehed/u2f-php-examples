<?php
declare(strict_types=1);

namespace Firehed\Webauthn\Endpoints;

use Firehed\API\Interfaces\EndpointInterface;
use Firehed\API\Traits\Input;
use Firehed\API\Traits\Request;
use Firehed\Input\Containers\SafeInput;
use Firehed\InputObjects;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class Index implements EndpointInterface
{
    // use Input\NoOptional;
    // use Input\NoRequired;
    use Request\Get;

    public function getUri(): string
    {
        return '/';
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
        // passthru index.html
        // Implement this
    }
}
