<?php

namespace App\Response;

class ViolationListResponse extends ErrorResponse {

    /**
     * List of violations
     * @var Violation[]
     */
    private $violations = [ ];

    /**
     * @param Violation[] $violations
     */
    public function __construct(array $violations) {
        parent::__construct('Validation failed.');

        $this->violations = $violations;
    }
}