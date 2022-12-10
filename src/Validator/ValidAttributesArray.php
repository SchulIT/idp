<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ValidAttributesArray extends Constraint {
    public string $messageNotFound = 'Attribute with name {{ name }} was not found.';
    public string $messageInvalidValue = 'Attribute {{ name }} has an invalid value. Value must be of type {{ type }}, {{ given }} given.';
    public string $messageInvalidArrayItem = 'Attribute {{ name }} has an invalid value at position {{ pos }}. Value must be one of {{ valid }}, {{ given }} given.';
}