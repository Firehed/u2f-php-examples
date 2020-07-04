<?php
declare(strict_types=1);

namespace Firehed\Webauthn;

class UserStorage
{
    private const STORAGE_PATH = 'users';

    public function get(string $username): ?User
    {
        $file = $this->getFile($username);
        if (!file_exists($file)) {
            return null;
        }

        $data = file_get_contents($file);
        assert($data !== false);
        $decoded = json_decode($data, true, 512, JSON_THROW_ON_ERROR);

        return User::fromJson($decoded);
    }

    public function save(User $user): bool
    {
        return file_put_contents(
            $this->getFile($user->getName()),
            json_encode($user, JSON_PRETTY_PRINT),
        ) !== false;
    }

    private function getFile(string $username): string
    {
        return sprintf('%s/%s.json', self::STORAGE_PATH, basename($username));
    }

}
