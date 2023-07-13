<?php

namespace App\Security\EmailConfirmation;

use Exception;
use Throwable;

class EmailAddressAlreadyInUseException extends Exception {
    public function __construct(string $email, int $code = 0, ?Throwable $previous = null) {
        parent::__construct(sprintf('E-mail address %s is already in use', $email), $code, $previous);
    }
}