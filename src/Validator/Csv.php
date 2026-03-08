<?php

namespace App\Validator;

use Attribute;

#[Attribute]
class Csv {
    public string $message = 'This is not valid CSV (%error%).';
}