<?php

namespace App\Import\UserRegistrationCode;

use App\Entity\UserRegistrationCode;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class UserRegistrationCodesData {

    /**
     * @Serializer\Type("array<App\Import\UserRegistrationCode\UserRegistrationCodeData>")
     * @Assert\Valid()
     * @var UserRegistrationCode[]
     */
    private $codes;

    /**
     * @return UserRegistrationCode[]
     */
    public function getCodes(): array {
        return $this->codes;
    }

    /**
     * @param UserRegistrationCode[] $codes
     * @return UserRegistrationCodesData
     */
    public function setCodes(array $codes): UserRegistrationCodesData {
        $this->codes = $codes;
        return $this;
    }
}