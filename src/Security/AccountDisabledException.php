<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\AccountStatusException;

/**
 * @codeCoverageIgnore
 */
class AccountDisabledException extends AccountStatusException {
    public function getMessageKey() {
        return 'account_disabled';
    }
}