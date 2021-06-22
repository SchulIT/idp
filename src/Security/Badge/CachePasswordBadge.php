<?php

namespace App\Security\Badge;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\BadgeInterface;

class CachePasswordBadge implements BadgeInterface {

    private $password;
    private $hasher;

    public function __construct(string $password, UserPasswordHasherInterface $hasher) {
        $this->password = $password;
        $this->hasher = $hasher;
    }

    public function getHasher(): UserPasswordHasherInterface {
        return $this->hasher;
    }

    public function getPassword(): string {
        return $this->password;
    }

    /**
     * @inheritDoc
     */
    public function isResolved(): bool {
        return true;
    }
}