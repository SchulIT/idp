<?php

namespace App\Response;

readonly class Violation {

    public string $property;
    public string $message;

    public function __construct(string $property, string $message) {
        $this->property = $property;
        $this->message = $message;
    }
}