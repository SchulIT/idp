<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class X509Certificate extends Constraint {
    public string $message = 'This is not a valid X509 Certificate';
}