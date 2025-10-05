<?php

declare(strict_types=1);

namespace App\Security;

use Override;
use Symfony\Component\Security\Core\Exception\AccountStatusException;

/**
 * @codeCoverageIgnore
 */
class AccountDisabledException extends AccountStatusException {
    #[Override]
    public function getMessageKey(): string {
        return 'account_disabled';
    }
}
