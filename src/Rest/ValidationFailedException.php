<?php

namespace App\Rest;

use Exception;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Throwable;

class ValidationFailedException extends Exception {
    public function __construct(private ConstraintViolationListInterface $constraintViolations, $message = "", $code = 0, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    public function getConstraintViolations(): ConstraintViolationListInterface {
        return $this->constraintViolations;
    }
}