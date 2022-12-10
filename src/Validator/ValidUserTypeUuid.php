<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ValidUserTypeUuid extends Constraint {
    public string $message = 'User type with uuid {{ uuid }} was not found.';
}