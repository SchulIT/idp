<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class Csv extends Constraint {
    public string $message = 'This is not valid CSV (%error%).';
}