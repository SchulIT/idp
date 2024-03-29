<?php

namespace App\Security;

class ActiveDirectoryUserInformation {
    /** @var string */
    private string $username;

    /** @var string */
    private string $userPrincipalName;

    /** @var string|null */
    private ?string $firstname = null;

    /** @var string|null */
    private ?string $lastname = null;

    /** @var string */
    private string $email;

    /** @var string */
    private string $guid;

    /** @var string|null */
    private ?string $uniqueId = null;

    /** @var string */
    private string $ou;

    /** @var string[] */
    private array $groups;

    public function getUsername(): string {
        return $this->username;
    }

    public function setUsername(string $username): ActiveDirectoryUserInformation {
        $this->username = $username;
        return $this;
    }

    public function getUserPrincipalName(): string {
        return $this->userPrincipalName;
    }

    public function setUserPrincipalName(string $userPrincipalName): ActiveDirectoryUserInformation {
        $this->userPrincipalName = $userPrincipalName;
        return $this;
    }

    public function getFirstname(): ?string {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): ActiveDirectoryUserInformation {
        $this->firstname = $firstname;
        return $this;
    }

    public function getLastname(): ?string {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): ActiveDirectoryUserInformation {
        $this->lastname = $lastname;
        return $this;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function setEmail(string $email): ActiveDirectoryUserInformation {
        $this->email = $email;
        return $this;
    }

    public function getGuid(): string {
        return $this->guid;
    }

    public function setGuid(string $guid): ActiveDirectoryUserInformation {
        $this->guid = $guid;
        return $this;
    }

    public function getUniqueId(): ?string {
        return $this->uniqueId;
    }

    public function setUniqueId(?string $uniqueId): ActiveDirectoryUserInformation {
        $this->uniqueId = $uniqueId;
        return $this;
    }

    public function getOu(): string {
        return $this->ou;
    }

    public function setOu(string $ou): ActiveDirectoryUserInformation {
        $this->ou = $ou;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getGroups(): array {
        return $this->groups;
    }

    /**
     * @param string[] $groups
     */
    public function setGroups(array $groups): ActiveDirectoryUserInformation {
        $this->groups = $groups;
        return $this;
    }
}