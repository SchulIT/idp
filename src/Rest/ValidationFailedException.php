<?php

declare(strict_types=1);

namespace App\Rest;

use Exception;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Throwable;

class ValidationFailedException extends Exception {
    public function __construct(private readonly ConstraintViolationListInterface $constraintViolations, $message = "", $code = 0, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    public function getConstraintViolations(): ConstraintViolationListInterface {
        return $this->constraintViolations;
    }
}
