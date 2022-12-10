<?php

namespace App\Response;

use JMS\Serializer\Annotation as Serializer;

class ViolationListResponse extends ErrorResponse {

    /**
     * @param Violation[] $violations
     */
    public function __construct(/**
     * List of violations
     * @Serializer\Type("array<App\Response\Violation>")
     * @Serializer\SerializedName("violations")
     */
    private array $violations) {
        parent::__construct('Validation failed.');
    }
}