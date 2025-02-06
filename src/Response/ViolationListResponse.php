<?php

namespace App\Response;

use JMS\Serializer\Annotation as Serializer;

readonly class ViolationListResponse extends ErrorResponse {

    /**
     * @param Violation[] $violations
     */
    public function __construct(#[Serializer\Type("array<App\Response\Violation>")]
    public array $violations) {
        parent::__construct('Validierung fehlgeschlagen.');
    }
}