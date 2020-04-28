<?php

namespace App\Rest;

use Symfony\Component\Validator\ConstraintViolationListInterface;
use Throwable;

class ValidationFailedException extends \Exception {
    private $constraintViolations;

    public function __construct(ConstraintViolationListInterface $constraintViolations, $message = "", $code = 0, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);

        $this->constraintViolations = $constraintViolations;
    }

    public function getConstraintViolations(): ConstraintViolationListInterface {
        return $this->constraintViolations;
    }
}