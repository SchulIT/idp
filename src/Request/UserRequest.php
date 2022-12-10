<?php

namespace App\Request;

use App\Validator\ValidUserTypeUuid;
use DateTime;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class UserRequest {

    /**
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank]
    private $username;

    /**
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank(allowNull: true)]
    private ?string $password = null;

    /**
     * @Serializer\Type("string")
     */
    private ?string $externalId = null;

    /**
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank]
    private $firstname;

    /**
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank]
    #[Assert\Email]
    private $email;

    /**
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank]
    private $lastname;

    /**
     * @Serializer\Type("string")
     */
    private ?string $grade = null;

    /**
     * @Serializer\Type("string")
     * @ValidUserTypeUuid()
     */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    private string $type;

    /**
     * @Serializer\Type("boolean")
     */
    private bool $isActive = true;

    /**
     * @Serializer\Type("DateTime")
     */
    private ?DateTime $enabledFrom = null;

    /**
     * @Serializer\Type("DateTime")
     */
    private ?DateTime $enabledUntil = null;

    /**
     * @return string
     */
    public function getUsername() {
        return $this->username;
    }

    public function getPassword(): ?string {
        return $this->password;
    }

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

    public function getGrade(): ?string {
        return $this->grade;
    }

    /**
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    public function isActive(): bool {
        return $this->isActive;
    }

    public function getEnabledFrom(): ?DateTime {
        return $this->enabledFrom;
    }

    public function getEnabledUntil(): ?DateTime {
        return $this->enabledUntil;
    }
}