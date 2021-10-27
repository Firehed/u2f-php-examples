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
    private string $id;
    private string $name;
    private string $hash = '';

    private array $registrations = [];

    public function __construct()
    {
        $this->id = self::uuidv4();
    }

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
            'id' => $this->id,
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
        return array_values(array_map(function ($regData) {
            $reg = new Registration();
            $reg->setCounter($regData['counter']);
            $reg->setKeyHandle(base64_decode($regData['key_handle']));
            $reg->setPublicKey(new ECPublicKey(base64_decode($regData['public_key'])));
            // att cert?
            $reg->setAttestationCertificate(
                new AttestationCertificate(base64_decode($regData['attestation_certificate'])),
            );

            return $reg;
        }, $this->registrations));
    }

    public static function fromJson(array $data): User
    {
        $user = new User();
        $user->id = $data['id'];
        $user->name = $data['name'];
        $user->hash = $data['hash'];
        $user->registrations = $data['registrations'];
        return $user;
    }

    private static function uuidv4(): string
    {
        $bytes = random_bytes(16);
        $bytes[6] = chr((ord($bytes[6])) & 0x0F | 0x40);
        $hex = bin2hex($bytes);
        return sprintf(
            '%s-%s-%s-%s-%s',
            substr($hex, 0, 8),
            substr($hex, 8, 4),
            substr($hex, 12, 4),
            substr($hex, 16, 4),
            substr($hex, 20, 12),
        );
    }


}
