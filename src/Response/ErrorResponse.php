<?php

namespace App\Response;

use JMS\Serializer\Annotation as Serializer;

class ErrorResponse {

    public function __construct(
        /**
         * Error message
         *
         * @Serializer\Type("string")
         * @Serializer\SerializedName("message")
         */
        private string $message,
        /**
         * Type of exception (optional).
         *
         * @Serializer\Type("string")
         * @Serializer\SerializedName("type")
         */
        private ?string $type = null
    )
    {
    }
}