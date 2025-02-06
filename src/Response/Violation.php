<?php

namespace App\Response;

readonly class Violation {

    public function __construct(public string $property, public string $message)
    {
    }
}