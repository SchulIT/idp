<?php

namespace App\Request;

use App\Validator\ValidUserTypeUuid;
use DateTime;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class UserRequest {

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     */
    private $username;

    /**
     * @Serializer\Type("string")
     * @var string|null
     */
    private $externalId;

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     */
    private $firstname;

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     */
    private $lastname;

    /**
     * @Serializer\Type("string")
     * @var string|null
     */
    private $grade;

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @Assert\Uuid()
     * @ValidUserTypeUuid()
     * @var string
     */
    private $type;

    /**
     * @Serializer\Type("boolean")
     */
    private $isActive = true;

    /**
     * @Serializer\Type("DateTime")
     * @var DateTime|null
     */
    private $enabledFrom;

    /**
     * @Serializer\Type("DateTime")
     * @var DateTime|null
     */
    private $enabledUntil;

    /**
     * @return string
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * @return string|null
     */
    public function getExternalId(): ?string {
        return $this->externalId;
    }

    /**
     * @return string
     */
    public function getFirstname() {
        return $this->firstname;
    }

    /**
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getLastname() {
        return $this->lastname;
    }

    /**
     * @return string|null
     */
    public function getGrade(): ?string {
        return $this->grade;
    }

    /**
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function isActive(): bool {
        return $this->isActive;
    }

    /**
     * @return DateTime|null
     */
    public function getEnabledFrom(): ?DateTime {
        return $this->enabledFrom;
    }

    /**
     * @return DateTime|null
     */
    public function getEnabledUntil(): ?DateTime {
        return $this->enabledUntil;
    }
}