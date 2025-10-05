<?php

declare(strict_types=1);

namespace App\Response;

readonly class ErrorResponse {

    public function __construct(
        /**
         * @var string Fehlermeldung
         */
        public string $message,
        /**
         * @var string|null Klasse der Exception (falls verfügbar)
         */
        public ?string $type = null
    )
    {
    }
}
