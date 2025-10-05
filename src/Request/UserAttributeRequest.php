<?php

declare(strict_types=1);

namespace App\Request;

use App\Validator\ValidAttributesArray;
use JMS\Serializer\Annotation as Serializer;

class UserAttributeRequest {

    /**
     * @var array Als Schlüssel wird der Name des Attributs angegeben. Der Inhalt ist entweder ein String oder
     * ein Stringarray (sofern mehrere Werte laut Attribute zugelassen sind).
     */
    #[Serializer\Type("array")]
    #[Serializer\Accessor(getter: 'getAttributes', setter: 'setAttributes')]
    #[ValidAttributesArray]
    private array $attributes = [ ];

    public function getAttributes(): array {
        return $this->attributes;
    }

    public function setAttributes(array $attributes): void {
        $this->attributes = $attributes;
    }
}
