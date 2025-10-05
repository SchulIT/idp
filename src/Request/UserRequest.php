<?php

declare(strict_types=1);

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

    public function getUsername(): string {
        return $this->username;
    }

    public function getPassword(): ?string {
        return $this->password;
    }

    public function getExternalId(): ?string {
        return $this->externalId;
    }

    public function getFirstname(): string {
        return $this->firstname;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getLastname(): string {
        return $this->lastname;
    }

    public function getGrade(): ?string {
        return $this->grade;
    }

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

    public function setUsername(string $username): void {
        $this->username = $username;
    }

    public function setPassword(?string $password): void {
        $this->password = $password;
    }

    public function setExternalId(?string $externalId): void {
        $this->externalId = $externalId;
    }

    public function setFirstname(string $firstname): void {
        $this->firstname = $firstname;
    }

    public function setEmail(string $email): void {
        $this->email = $email;
    }

    public function setLastname(string $lastname): void {
        $this->lastname = $lastname;
    }

    public function setGrade(?string $grade): void {
        $this->grade = $grade;
    }

    public function setType(string $type): void {
        $this->type = $type;
    }

    public function setIsActive(bool $isActive): void {
        $this->isActive = $isActive;
    }

    public function setEnabledFrom(?DateTime $enabledFrom): void {
        $this->enabledFrom = $enabledFrom;
    }

    public function setEnabledUntil(?DateTime $enabledUntil): void {
        $this->enabledUntil = $enabledUntil;
    }
}
