<?php
declare(strict_types=1);

namespace Firehed\Webauthn;

use JsonSerializable;
use Firehed\U2F\{
    AttestationCertificate,
    ECPublicKey,
    Registration,
    RegistrationInterface,
};

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
        // run/apply password_needs_rehash here?

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

    public function updateRegistration(RegistrationInterface $reg): void
    {
        $this->registrations[base64_encode($reg->getKeyHandleBinary())]['counter'] = $reg->getCounter();
    }

    public function addRegistration(RegistrationInterface $reg)
    {
        $this->registrations[base64_encode($reg->getKeyHandleBinary())] = [
            'counter' => $reg->getCounter(),
            'key_handle' => base64_encode($reg->getKeyHandleBinary()),
            'public_key' => base64_encode($reg->getPublicKey()->getBinary()),
            'attestation_certificate' => base64_encode($reg->getAttestationCertificate()->getBinary()),
        ];
    }

    /** @return RegistrationInterface[] */
    public function getRegistrations(): array
    {
        return array_map(function ($regData) {
            $reg = new Registration();
            $reg->setCounter($regData['counter']);
            $reg->setKeyHandle(base64_decode($regData['key_handle']));
            $reg->setPublicKey(new ECPublicKey(base64_decode($regData['public_key'])));
            // att cert?
            $reg->setAttestationCertificate(
                new AttestationCertificate(base64_decode($regData['attestation_certificate'])),
            );

            return $reg;
        }, $this->registrations);
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
