<?php

namespace App\Security\Badge;

use Symfony\Component\Security\Http\Authenticator\Passport\Badge\BadgeInterface;

abstract class AbstractBadge implements BadgeInterface {
    protected $isResolved = false;

    public function markResolved(): void {
        $this->isResolved = true;
    }

    public function isResolved(): bool {
        return $this->isResolved;
    }
}