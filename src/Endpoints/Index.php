<?php
declare(strict_types=1);

namespace Firehed\Webauthn\Endpoints;

use Firehed\API\Interfaces\EndpointInterface;
use Firehed\API\Traits;
use Firehed\Input\Containers\SafeInput;
use Firehed\InputObjects;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class Index implements EndpointInterface
{
    // use Input\NoOptional;
    // use Input\NoRequired;
    use Traits\Request\Get;
    use Traits\ResponseBuilder;

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
        return $this->htmlResponse(file_get_contents('public/index.html'));
    }
}
