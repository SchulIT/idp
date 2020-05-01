<?php

namespace App\Request;

use App\Validator\ValidAttributesArray;
use JMS\Serializer\Annotation as Serializer;

class UserAttributeRequest {

    /**
     * @Serializer\SerializedName("attributes")
     * @Serializer\Type("array")
     * @ValidAttributesArray()
     * @var array
     */
    private $attributes = [ ];

    /**
     * @return array
     */
    public function getAttributes(): array {
        return $this->attributes;
    }
}