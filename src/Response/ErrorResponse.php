<?php

namespace App\Response;

use JMS\Serializer\Annotation as Serializer;

class ErrorResponse {

    /**
     * Type of exception (optional).
     *
     * @Serializer\Type("string")
     * @Serializer\SerializedName("type")
     * @var string
     */
    private $type;

    /**
     * Error message
     *
     * @Serializer\Type("string")
     * @Serializer\SerializedName("message")
     * @var string
     */
    private $message;

    public function __construct(string $message, ?string $type = null) {
        $this->message = $message;
        $this->type = $type;
    }
}