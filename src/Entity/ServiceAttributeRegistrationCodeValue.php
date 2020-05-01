<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class ServiceAttributeRegistrationCodeValue implements ServiceAttributeValueInterface {
    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="ServiceAttribute")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $attribute;

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="RegistrationCode", inversedBy="attributes")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $registrationCode;

    /**
     * @ORM\Column(type="object", nullable=true)
     */
    private $value;

    /**
     * @return ServiceAttribute
     */
    public function getAttribute() {
        return $this->attribute;
    }

    /**
     * @param ServiceAttribute $attribute
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
     * @param RegistrationCode $registrationCode
     * @return ServiceAttributeRegistrationCodeValue
     */
    public function setRegistrationCode(RegistrationCode $registrationCode) {
        $this->registrationCode = $registrationCode;
        return $this;
    }

    /**
     * @return string|string[]|int
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * @param string|string[]|int $value
     */
    public function setValue($value) {
        $this->value = $value;
    }
}