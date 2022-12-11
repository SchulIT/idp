<?php

namespace App\Request;

use App\Validator\ValidUserTypeUuid;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationCode {

    #[Assert\NotBlank]
    private ?string $code = null;

    #[Assert\NotBlank]
    private ?string $username = null;

    #[Assert\NotBlank(allowNull: true)]
    private ?string $firstname = null;

    #[Assert\NotBlank(allowNull: true)]
    private ?string $lastname = null;

    #[Assert\Email]
    private ?string $email = null;

    #[Assert\NotBlank(allowNull: true)]
    private ?string $grade = null;

    /**
     * @ValidUserTypeUuid()
     */
    #[Assert\Uuid]
    private ?string $type = null;

    /**
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

    /**
     * @return string[]
     */
    public function getAttributes(): array {
        return $this->attributes;
    }

    /**
     * @param string|null $code
     */
    public function setCode(?string $code): void {
        $this->code = $code;
    }

    /**
     * @param string|null $username
     */
    public function setUsername(?string $username): void {
        $this->username = $username;
    }

    /**
     * @param string|null $firstname
     */
    public function setFirstname(?string $firstname): void {
        $this->firstname = $firstname;
    }

    /**
     * @param string|null $lastname
     */
    public function setLastname(?string $lastname): void {
        $this->lastname = $lastname;
    }

    /**
     * @param string|null $email
     */
    public function setEmail(?string $email): void {
        $this->email = $email;
    }

    /**
     * @param string|null $grade
     */
    public function setGrade(?string $grade): void {
        $this->grade = $grade;
    }

    /**
     * @param string|null $type
     */
    public function setType(?string $type): void {
        $this->type = $type;
    }

    /**
     * @param array $attributes
     */
    public function setAttributes(array $attributes): void {
        $this->attributes = $attributes;
    }
}