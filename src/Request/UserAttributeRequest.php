<?php

namespace App\Request;

use App\Validator\ValidAttributesArray;

class UserAttributeRequest {

    /**
     * @ValidAttributesArray()
     */
    private array $attributes = [ ];

    public function getAttributes(): array {
        return $this->attributes;
    }

    /**
     * @param array $attributes
     */
    public function setAttributes(array $attributes): void {
        $this->attributes = $attributes;
    }
}