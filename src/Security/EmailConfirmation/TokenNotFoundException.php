<?php

namespace App\Security\EmailConfirmation;

use Exception;
use Throwable;

class TokenNotFoundException extends Exception {
    private string $token;

    public function __construct(string $token, $code = 0, Throwable $previous = null) {
        parent::__construct(sprintf('Email confirmation token %s was not found.', $token), $code, $previous);

        $this->token = $token;
    }

    public function getToken(): string {
        return $this->token;
    }
}