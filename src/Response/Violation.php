<?php

namespace App\Response;

use JMS\Serializer\Annotation as Serializer;

class Violation {

    public readonly string $property;
    public readonly string $message;

    public function __construct(string $property, string $message) {
        $this->property = $property;
        $this->message = $message;
    }
}