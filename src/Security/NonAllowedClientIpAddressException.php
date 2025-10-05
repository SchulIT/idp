<?php

declare(strict_types=1);

namespace App\Security;

use Override;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class NonAllowedClientIpAddressException extends AuthenticationException {
    #[Override]
    public function getMessageKey(): string {
        return 'This IP Address is not allowed.';
    }
}
