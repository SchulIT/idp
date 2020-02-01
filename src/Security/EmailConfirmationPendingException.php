<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\AccountStatusException;

/**
 * @codeCoverageIgnore
 */
class EmailConfirmationPendingException extends AccountStatusException {
    public function getMessageKey() {
        return 'email_confirmation_pending';
    }
}