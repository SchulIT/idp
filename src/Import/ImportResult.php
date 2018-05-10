<?php

namespace App\Import;

use JMS\Serializer\Annotation as Serializer;

class ImportResult implements ImportResultInterface {

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