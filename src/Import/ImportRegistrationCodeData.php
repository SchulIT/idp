<?php

namespace App\Import;

use App\Entity\RegistrationCode;
use Symfony\Component\Validator\Constraints as Assert;

class ImportRegistrationCodeData extends AbstractImportData {

    /**
     * @Assert\Valid(groups={"step_two", "RegistrationCode"})
     * @var RegistrationCode[]
     */
    private $codes = [ ];

    /**
     * @return RegistrationCode[]
     */
    public function getCodes(): array {
        return $this->codes;
    }

    /**
     * @param RegistrationCode[] $codes
     * @return ImportRegistrationCodeData
     */
    public function setCodes(array $codes): ImportRegistrationCodeData {
        $this->codes = $codes;
        return $this;
    }
}