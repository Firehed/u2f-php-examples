<?php
declare(strict_types=1);

namespace Firehed\Webauthn\Endpoints;

use Firehed\API\Interfaces\EndpointInterface;
use Firehed\API\Traits;
use Firehed\Input\Containers\SafeInput;
use Firehed\InputObjects\Text;
use Firehed\Webauthn\UserStorage;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class Login implements EndpointInterface
{
    use Traits\Request\Post;
    use Traits\ResponseBuilder;

    private UserStorage $userStorage;

    public function __construct(UserStorage $s)
    {
        $this->userStorage = $s;
    }

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
        $user = $this->userStorage->get($input['username']);

        $isValidPassword = $user->isPasswordCorrect($input['password']);

        if (!$isValidPassword) {
            return $this->jsonResponse('Bad login', 403);
        }

        $_SESSION['USER_NAME'] = $user->getName();

        return $this->jsonResponse([
            'ok',
        ], 200);
    }
}
