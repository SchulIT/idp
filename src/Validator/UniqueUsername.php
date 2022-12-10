<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UniqueUsername extends Constraint {

    /**
     * Type of the user (user or ad)
     */
    public string $type = 'user';

    public string $message = 'Username already exists or user does not match given type.';
}