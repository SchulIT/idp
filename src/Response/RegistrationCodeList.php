<?php

namespace App\Response;

use JMS\Serializer\Annotation as Serializer;

class RegistrationCodeList {

    /**
     * List of UUIDs of all registration codes
     * @Serializer\SerializedName("codes")
     * @Serializer\Type("array<string>")
     *
     * @var string[]
     */
    private $codes;

    /**
     * @param string[] $codes
     */
    public function __construct(array $codes) {
        $this->codes = $codes;
    }
}