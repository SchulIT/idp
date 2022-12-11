<?php

namespace App\Response;

class ErrorResponse {


    public function __construct(

        private readonly string $message,
        /**
         * Type of exception (optional).
         */
        private readonly ?string $type = null
    )
    {
    }

    public function getMessage(): string {
        return $this->message;
    }

    public function getType(): ?string {
        return $this->type;
    }
}