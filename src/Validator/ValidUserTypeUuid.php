<?php

declare(strict_types=1);

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class ValidUserTypeUuid extends Constraint {
    public string $message = 'User type with uuid {{ uuid }} was not found.';
}
