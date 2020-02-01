<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class ServiceAttributeUserRegistrationCodeValue implements ServiceAttributeValueInterface {
    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="ServiceAttribute")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $attribute;

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="UserRegistrationCode", inversedBy="attributes")
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
     * @return ServiceAttributeUserRegistrationCodeValue
     */
    public function setAttribute(ServiceAttribute $attribute) {
        $this->attribute = $attribute;
        return $this;
    }

    /**
     * @return UserRegistrationCode
     */
    public function getRegistrationCode() {
        return $this->registrationCode;
    }

    /**
     * @param UserRegistrationCode $registrationCode
     * @return ServiceAttributeUserRegistrationCodeValue
     */
    public function setRegistrationCode(UserRegistrationCode $registrationCode) {
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