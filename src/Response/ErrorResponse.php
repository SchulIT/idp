<?php

namespace App\Response;

class ErrorResponse {

    /**
     * @var string Fehlermeldung
     */
    public readonly string $message;

    /**
     * @var string|null Klasse der Exception (falls verfügbar)
     */
    public readonly ?string $type;


    public function __construct(string $message, ?string $type = null)
    {
        $this->message = $message;
        $this->type = $type;
    }
}