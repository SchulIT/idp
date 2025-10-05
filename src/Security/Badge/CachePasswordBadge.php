<?php

declare(strict_types=1);

namespace App\Security\Badge;

use SensitiveParameter;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\BadgeInterface;

readonly class CachePasswordBadge implements BadgeInterface {

    public function __construct(#[SensitiveParameter] private string $password, private UserPasswordHasherInterface $hasher)
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
