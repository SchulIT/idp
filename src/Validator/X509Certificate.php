<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class X509Certificate extends Constraint {
    public string $message = 'This is not a valid X509 Certificate';
}