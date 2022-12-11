<?php

namespace App\Response;

use JMS\Serializer\Annotation as Serializer;

class RegistrationCodeList {

    /**
     * @param string[] $codes
     */
    public function __construct(
        /**
         * List of UUIDs of all registration codes
         */
        private readonly array $codes
    )
    {
    }

    /**
     * @return string[]
     */
    public function getCodes(): array {
        return $this->codes;
    }
}