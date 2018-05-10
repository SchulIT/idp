<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class BcryptPassword extends Constraint {
    public $message = 'This is not a valid bcrypt password.';
}