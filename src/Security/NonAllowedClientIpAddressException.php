<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

class NonAllowedClientIpAddressException extends AuthenticationException {
    public function getMessageKey() {
        return 'This IP Address is not allowed.';
    }
}