<?php

namespace App\Import;

use JMS\Serializer\Annotation as Serializer;

class FailedImportResult implements ImportResultInterface {

    /**
     * @Serializer\Accessor(getter="getSerializedException")
     */
    private $exception;

    public function __construct(\Exception $exception) {
        $this->exception = $exception;
    }

    public function getException() {
        return $this->exception;
    }

    public function getSerializedException() {
        return [
            'code' => $this->exception->getCode(),
            'message' => $this->exception->getMessage()
        ];
    }

    public function isSuccessful() {
        return false;
    }
}