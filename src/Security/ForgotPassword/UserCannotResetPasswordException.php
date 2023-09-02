<?php

namespace App\Security\ForgotPassword;

use Exception;
use Throwable;

class UserCannotResetPasswordException extends Exception {
    public function __construct(private readonly Reason $reason, string $message = "", int $code = 0, ?Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return Reason
     */
    public function getReason(): Reason {
        return $this->reason;
    }#
}