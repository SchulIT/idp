<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class UniqueUsername extends Constraint {

    /**
     * Type of the user (user or ad)
     */
    public string $type = 'user';

    public string $message = 'Username already exists or user does not match given type.';
}