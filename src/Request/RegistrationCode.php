<?php

namespace App\Request;

use App\Validator\ValidUserTypeUuid;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationCode {

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @var string|null
     */
    private $code;

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @var string|null
     */
    private $username;

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
     * @Serializer\Type("string")
     * @Assert\Uuid()
     * @ValidUserTypeUuid()
     * @var string|null
     */
    private $type;

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank(allowNull=true)
     * @var string|null
     */
    private $externalId;

    /**
     * @Serializer\Type("array<string, string>")
     * @var string[]
     */
    private $attributes = [ ];

    /**
     * @return string|null
     */
    public function getCode(): ?string {
        return $this->code;
    }

    /**
     * @return string|null
     */
    public function getUsername(): ?string {
        return $this->username;
    }

    /**
     * @return string|null
     */
    public function getFirstname(): ?string {
        return $this->firstname;
    }

    /**
     * @return string|null
     */
    public function getLastname(): ?string {
        return $this->lastname;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string {
        return $this->email;
    }

    /**
     * @return string|null
     */
    public function getGrade(): ?string {
        return $this->grade;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string {
        return $this->type;
    }

    /**
     * @return string|null
     */
    public function getExternalId(): ?string {
        return $this->externalId;
    }

    /**
     * @return string[]
     */
    public function getAttributes(): array {
        return $this->attributes;
    }
}