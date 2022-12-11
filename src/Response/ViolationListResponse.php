<?php

namespace App\Response;

use JMS\Serializer\Annotation as Serializer;

class ViolationListResponse extends ErrorResponse {

    /**
     * @param Violation[] $violations
     */
    public function __construct(
        /**
         * List of violations
         */
    private readonly array $violations) {
        parent::__construct('Validation failed.');
    }

    /**
     * @return Violation[]
     */
    public function getViolations(): array {
        return $this->violations;
    }
}