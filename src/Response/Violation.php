<?php

namespace App\Response;

use JMS\Serializer\Annotation as Serializer;

class Violation {

    public function __construct(
        /**
         * Property on which this violation occurred.
         */
        private readonly string $property,
        /**
         * Violation message.
         */
        private readonly string $message
    )
    {
    }

    public function getProperty(): string {
        return $this->property;
    }

    public function getMessage(): string {
        return $this->message;
    }
}