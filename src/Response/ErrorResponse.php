<?php

namespace App\Response;

readonly class ErrorResponse {

    /**
     * @var string Fehlermeldung
     */
    public string $message;

    /**
     * @var string|null Klasse der Exception (falls verfügbar)
     */
    public ?string $type;


    public function __construct(string $message, ?string $type = null)
    {
        $this->message = $message;
        $this->type = $type;
    }
}