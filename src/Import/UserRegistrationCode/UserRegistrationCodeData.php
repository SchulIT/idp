<?php

namespace App\Import\UserRegistrationCode;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class UserRegistrationCodeData {

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @var string
     */
    private $code;

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank(allowNull=true)
     * @var string|null
     */
    private $firstname;

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank(allowNull=true)
     * @var string|null
     */
    private $lastname;

    /**
     * @Serializer\Type("string")
     * @Assert\Email()
     * @var string|null
     */
    private $email;

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank(allowNull=true)
     * @var string|null
     */
    private $grade;

    /**
     * @Serializer\Type("int")
     */
    private $type;

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank(allowNull=true)
     * @var string|null
     */
    private $internalId;

    /**
     * @Serializer\Type("array<string, string>")
     * @var string[]
     */
    private $attributes = [ ];

    /**
     * @return string
     */
    public function getCode(): string {
        return $this->code;
    }

    /**
     * @param string $code
     * @return UserRegistrationCodeData
     */
    public function setCode(string $code): UserRegistrationCodeData {
        $this->code = $code;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFirstname(): ?string {
        return $this->firstname;
    }

    /**
     * @param string|null $firstname
     * @return UserRegistrationCodeData
     */
    public function setFirstname(?string $firstname): UserRegistrationCodeData {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastname(): ?string {
        return $this->lastname;
    }

    /**
     * @param string|null $lastname
     * @return UserRegistrationCodeData
     */
    public function setLastname(?string $lastname): UserRegistrationCodeData {
        $this->lastname = $lastname;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string {
        return $this->email;
    }

    /**
     * @param string|null $email
     * @return UserRegistrationCodeData
     */
    public function setEmail(?string $email): UserRegistrationCodeData {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getGrade(): ?string {
        return $this->grade;
    }

    /**
     * @param string|null $grade
     * @return UserRegistrationCodeData
     */
    public function setGrade(?string $grade): UserRegistrationCodeData {
        $this->grade = $grade;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @param mixed $type
     * @return UserRegistrationCodeData
     */
    public function setType($type) {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getInternalId(): ?string {
        return $this->internalId;
    }

    /**
     * @param string|null $internalId
     * @return UserRegistrationCodeData
     */
    public function setInternalId(?string $internalId): UserRegistrationCodeData {
        $this->internalId = $internalId;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getAttributes(): array {
        return $this->attributes;
    }

    /**
     * @param string[] $attributes
     * @return UserRegistrationCodeData
     */
    public function setAttributes(array $attributes): UserRegistrationCodeData {
        $this->attributes = $attributes;
        return $this;
    }
}