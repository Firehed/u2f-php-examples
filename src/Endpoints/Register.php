<?php
declare(strict_types=1);

namespace Firehed\Webauthn\Endpoints;

use Firehed\API\Interfaces\EndpointInterface;
use Firehed\API\Traits;
use Firehed\Input\Containers\SafeInput;
use Firehed\InputObjects\Text;
use Firehed\Webauthn\{User, UserStorage};
use Psr\Http\Message\ResponseInterface;
use Throwable;

class Register implements EndpointInterface
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
        $user = $this->userStorage->get($input['username']);
        if ($user) {
            return $this->jsonResponse('Already registered', 409);
        }

        $user = new User();
        $user->setName($input['username']);
        $user->setPassword($input['password']);

        $this->userStorage->save($user);

        return $this->jsonResponse($user);
    }
}
