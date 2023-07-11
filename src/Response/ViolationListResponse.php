<?php

namespace App\Response;

use JMS\Serializer\Annotation as Serializer;

readonly class ViolationListResponse extends ErrorResponse {

    #[Serializer\Type("array<App\Response\Violation>")]
    public array $violations;

    /**
     * @param Violation[] $violations
     */
    public function __construct(array $violations) {
        parent::__construct('Validierung fehlgeschlagen.');
        $this->violations = $violations;
    }
}