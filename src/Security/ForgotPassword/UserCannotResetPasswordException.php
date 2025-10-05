<?php

declare(strict_types=1);

namespace App\Security\ForgotPassword;

use Exception;
use Throwable;

class UserCannotResetPasswordException extends Exception {
    public function __construct(private readonly Reason $reason, string $message = "", int $code = 0, ?Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    public function getReason(): Reason {
        return $this->reason;
    }#
}
