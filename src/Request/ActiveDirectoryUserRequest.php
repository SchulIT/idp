<?php

declare(strict_types=1);

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;

class ActiveDirectoryUserRequest {

    #[Assert\NotBlank]
    #[Assert\Uuid]
    private string $objectGuid;

    #[Assert\NotBlank]
    private string $samAccountName;

    #[Assert\NotBlank]
    private string $userPrincipalName;

    #[Assert\NotBlank]
    private string $firstname;

    #[Assert\NotBlank]
    private string $lastname;

    #[Assert\Email]
    #[Assert\NotBlank]
    private string $email;

    #[Assert\NotBlank]
    private string $ou;

    /**
     * @var string[]
     */
    private array $groups;

    public function getObjectGuid(): ?string {
        return $this->objectGuid;
    }

    public function getSamAccountName(): ?string {
        return $this->samAccountName;
    }

    public function getUserPrincipalName(): ?string {
        return $this->userPrincipalName;
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

    public function getOu(): ?string {
        return $this->ou;
    }

    /**
     * @return string[]
     */
    public function getGroups(): array {
        return $this->groups;
    }

    public function setObjectGuid(string $objectGuid): void {
        $this->objectGuid = $objectGuid;
    }

    public function setSamAccountName(string $samAccountName): void {
        $this->samAccountName = $samAccountName;
    }

    public function setUserPrincipalName(string $userPrincipalName): void {
        $this->userPrincipalName = $userPrincipalName;
    }

    public function setFirstname(string $firstname): void {
        $this->firstname = $firstname;
    }

    public function setLastname(string $lastname): void {
        $this->lastname = $lastname;
    }

    public function setEmail(string $email): void {
        $this->email = $email;
    }

    public function setOu(string $ou): void {
        $this->ou = $ou;
    }

    public function setGroups(array $groups): void {
        $this->groups = $groups;
    }
}
