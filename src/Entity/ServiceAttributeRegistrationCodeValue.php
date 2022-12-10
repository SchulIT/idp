<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class ServiceAttributeRegistrationCodeValue implements ServiceAttributeValueInterface {
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: ServiceAttribute::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private $attribute;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: RegistrationCode::class, inversedBy: 'attributes')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private $registrationCode;

    #[ORM\Column(type: 'object', nullable: true)]
    private $value;

    /**
     * @return ServiceAttribute
     */
    public function getAttribute() {
        return $this->attribute;
    }

    /**
     * @return ServiceAttributeRegistrationCodeValue
     */
    public function setAttribute(ServiceAttribute $attribute) {
        $this->attribute = $attribute;
        return $this;
    }

    /**
     * @return RegistrationCode
     */
    public function getRegistrationCode() {
        return $this->registrationCode;
    }

    /**
     * @return ServiceAttributeRegistrationCodeValue
     */
    public function setRegistrationCode(RegistrationCode $registrationCode) {
        $this->registrationCode = $registrationCode;
        return $this;
    }

    /**
     * @return string|string[]|int
     */
    public function getValue(): string|array|int {
        return $this->value;
    }

    /**
     * @param string|string[]|int $value
     */
    public function setValue(string|array|int $value): ServiceAttributeRegistrationCodeValue {
        $this->value = $value;
        return $this;
    }
}