<?php

namespace App\Security\Badge;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\BadgeInterface;

class CachePasswordBadge implements BadgeInterface {

    public function __construct(private string $password, private UserPasswordHasherInterface $hasher)
    {
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