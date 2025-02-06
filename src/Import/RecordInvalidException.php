<?php

namespace App\Import;

use Exception;
use Throwable;

class RecordInvalidException extends Exception {
    private readonly int $index;

    private readonly string $field;

    public function __construct(int $index, string $field, $code = 0, Throwable $previous = null) {
        parent::__construct(sprintf('Record %d has an invalid field %s', $index, $field), $code, $previous);

        $this->index = $index;
        $this->field = $field;
    }

    public function getIndex(): int {
        return $this->index;
    }

    public function getField(): string {
        return $this->field;
    }
}