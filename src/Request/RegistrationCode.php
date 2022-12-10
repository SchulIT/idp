<?php

namespace App\Request;

use App\Validator\ValidUserTypeUuid;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationCode {

    /**
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank]
    private ?string $code = null;

    /**
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank]
    private ?string $username = null;

    /**
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank(allowNull: true)]
    private ?string $firstname = null;

    /**
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank(allowNull: true)]
    private ?string $lastname = null;

    /**
     * @Serializer\Type("string")
     */
    #[Assert\Email]
    private ?string $email = null;

    /**
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank(allowNull: true)]
    private ?string $grade = null;

    /**
     * @Serializer\Type("string")
     * @ValidUserTypeUuid()
     */
    #[Assert\Uuid]
    private ?string $type = null;

    /**
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank(allowNull: true)]
    private ?string $externalId = null;

    /**
     * @Serializer\Type("array<string, string>")
     * @var string[]
     */
    private array $attributes = [ ];

    public function getCode(): ?string {
        return $this->code;
    }

    public function getUsername(): ?string {
        return $this->username;
    }

    public function getFirstname(): ?string {
        return $this->firstname;
    }

    public function getLastname(): ?string {
        return $this->lastname;
    }

    public function getEmail(): ?string {
        return $this->email;
    }

    public function getGrade(): ?string {
        return $this->grade;
    }

    public function getType(): ?string {
        return $this->type;
    }

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