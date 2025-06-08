<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class AllowedEmailDomain extends Constraint {
    public string $message = 'This e-mail domain is not allowed.';
}