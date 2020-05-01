<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ValidAttributesArray extends Constraint {
    public $messageNotFound = 'Attribute with name {{ name }} was not found.';
    public $messageInvalidValue = 'Attribute {{ name }} has an invalid value. Value must be of type {{ type }}, {{ given }} given.';
    public $messageInvalidArrayItem = 'Attribute {{ name }} has an invalid value at position {{ pos }}. Value must be one of {{ valid }}, {{ given }} given.';
}