<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\AccountStatusException;

/**
 * @codeCoverageIgnore
 */
class EmailConfirmationPendingException extends AccountStatusException {
    public function getMessageKey(): string {
        return 'email_confirmation_pending';
    }
}