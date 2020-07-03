<?php
declare(strict_types=1);

namespace Firehed\Webauthn;

use JsonSerializable;

class User implements JsonSerializable
{
    private string $name;
    private string $hash = '';

    private array $registrations = [];

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setPassword(string $password): void
    {
        $this->hash = password_hash($password, PASSWORD_DEFAULT);
    }

    public function isPasswordCorrect(string $password): bool
    {
        return password_verify($password, $this->hash);
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'hash' => $this->hash,
            'registrations' => $this->registrations,
        ];
    }

    public static function fromJson(array $data): User
    {
        $user = new User();
        $user->name = $data['name'];
        $user->hash = $data['hash'];
        $user->registrations = $data['registrations'];
        return $user;
    }
}
