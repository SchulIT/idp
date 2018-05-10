<?php

namespace App\Import\Json;

use JMS\Serializer\Annotation as Serializer;

abstract class Response {
    /**
     * @Serializer\Accessor(getter="isSuccessful")
     * @Serializer\SerializedName("success")
     */
    private $isSuccessful = false;

    public function __construct($isSuccessful) {
        $this->isSuccessful = $isSuccessful;
    }

    public function isSuccessful() {
        return $this->isSuccessful;
    }
}