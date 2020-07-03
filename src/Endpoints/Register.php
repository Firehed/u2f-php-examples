<?php
declare(strict_types=1);

namespace Firehed\Webauthn\Endpoints;

use Firehed\API\Interfaces\EndpointInterface;
use Firehed\API\Traits;
use Firehed\Input\Containers\SafeInput;
use Firehed\InputObjects\Text;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class Register implements EndpointInterface
{
    use Traits\Request\Post;
    use Traits\ResponseBuilder;

    public function getUri(): string
    {
        return '/register';
    }

    public function getRequiredInputs(): array
    {
        return [
            'username' => (new Text())->setMin(1),
            'password' => (new Text())->setMin(1),
        ];
    }

    public function getOptionalInputs(): array
    {
        return [];
    }

    public function execute(SafeInput $input): ResponseInterface
    {
        $file = sprintf('users/%s.json', $input['username']);
        if (file_exists($file)) {
            return $this->jsonResponse('Already registered', 409);
        }
        $hash = password_hash($input['password'], PASSWORD_DEFAULT);
        // write to db

        $data = [
            'user' => $input['username'],
            'hash' => $hash,
        ];

        file_put_contents($file, json_encode($data));
        
        return $this->jsonResponse($data);
    }
}
