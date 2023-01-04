<?php

namespace App\Response;

use JMS\Serializer\Annotation as Serializer;

class ViolationListResponse extends ErrorResponse {

    #[Serializer\Type("array<App\Response\Violation>")]
    public readonly array $violations;

    /**
     * @param Violation[] $violations
     */
    public function __construct(array $violations) {
        parent::__construct('Validierung fehlgeschlagen.');
        $this->violations = $violations;
    }
}