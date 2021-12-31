<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\AccountStatusException;

/**
 * @codeCoverageIgnore
 */
class AccountDisabledException extends AccountStatusException {
    public function getMessageKey(): string {
        return 'account_disabled';
    }
}