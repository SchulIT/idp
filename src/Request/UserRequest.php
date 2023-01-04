<?php

namespace App\Request;

use App\Validator\ValidUserTypeUuid;
use DateTime;
use Symfony\Component\Validator\Constraints as Assert;

class UserRequest {

    /** @var string Benutzername des Benutzers (muss eine gültige E-Mail-Adresse sein) */
    #[Assert\NotBlank]
    #[Assert\Email]
    private string $username;

    /** @var string|null Passwort des Benutzers - wenn leer: TODO */
    #[Assert\NotBlank(allowNull: true)]
    private ?string $password = null;

    /** @var string|null Eine (optionale) externe ID zum Wiedererkennen des Benutzers */
    private ?string $externalId = null;

    /** @var string Vorname */
    #[Assert\NotBlank]
    private string $firstname;

    /** @var string Nachname */
    #[Assert\NotBlank]
    private string $lastname;

    /** @var string E-Mail Adresse des Benutzers */
    #[Assert\NotBlank]
    #[Assert\Email]
    private string $email;

    /** @var string|null Klasse */
    private ?string $grade = null;
    
    /**
     * @var string UUID des Benutzertyps
     */
    #[ValidUserTypeUuid]
    #[Assert\NotBlank]
    #[Assert\Uuid]
    private string $type;

    private bool $isActive = true;

    private ?DateTime $enabledFrom = null;

    private ?DateTime $enabledUntil = null;

    /**
     * @return string
     */
    public function getUsername(): string {
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
    public function getFirstname(): string {
        return $this->firstname;
    }

    /**
     * @return string
     */
    public function getEmail(): string {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getLastname(): string {
        return $this->lastname;
    }

    public function getGrade(): ?string {
        return $this->grade;
    }

    /**
     * @return string
     */
    public function getType(): string {
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

    /**
     * @param string $username
     */
    public function setUsername(string $username): void {
        $this->username = $username;
    }

    /**
     * @param string|null $password
     */
    public function setPassword(?string $password): void {
        $this->password = $password;
    }

    /**
     * @param string|null $externalId
     */
    public function setExternalId(?string $externalId): void {
        $this->externalId = $externalId;
    }

    /**
     * @param string $firstname
     */
    public function setFirstname(string $firstname): void {
        $this->firstname = $firstname;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void {
        $this->email = $email;
    }

    /**
     * @param string $lastname
     */
    public function setLastname(string $lastname): void {
        $this->lastname = $lastname;
    }

    /**
     * @param string|null $grade
     */
    public function setGrade(?string $grade): void {
        $this->grade = $grade;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void {
        $this->type = $type;
    }

    /**
     * @param bool $isActive
     */
    public function setIsActive(bool $isActive): void {
        $this->isActive = $isActive;
    }

    /**
     * @param DateTime|null $enabledFrom
     */
    public function setEnabledFrom(?DateTime $enabledFrom): void {
        $this->enabledFrom = $enabledFrom;
    }

    /**
     * @param DateTime|null $enabledUntil
     */
    public function setEnabledUntil(?DateTime $enabledUntil): void {
        $this->enabledUntil = $enabledUntil;
    }
}