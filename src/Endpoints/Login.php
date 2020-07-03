<?php
declare(strict_types=1);

namespace Firehed\Webauthn\Endpoints;

use Firehed\API\Interfaces\EndpointInterface;
use Firehed\API\Traits;
use Firehed\Input\Containers\SafeInput;
use Firehed\InputObjects\Text;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class Login implements EndpointInterface
{
    use Traits\Request\Post;
    use Traits\ResponseBuilder;

    public function getUri(): string
    {
        return '/login';
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
        // Read from db
        $file = sprintf('users/%s.json', $input['username']);
        if (!file_exists($file)) {
            return $this->jsonResponse('Bad login', 403);
        }

        $user = json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);

        $isValidPassword = password_verify($input['password'], $user['hash']);

        if (!$isValidPassword) {
            return $this->jsonResponse('Bad login', 403);
        }

        // run/apply password_needs_rehash here

        // todo setcookie/session

        return $this->jsonResponse([
            'ok',
        ], 200);
    }
}
