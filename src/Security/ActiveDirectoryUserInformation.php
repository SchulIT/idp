<?php

namespace App\Security;

class ActiveDirectoryUserInformation {
    /** @var string */
    private $username;

    /** @var string */
    private $userPrincipalName;

    /** @var string|null */
    private $firstname;

    /** @var string|null */
    private $lastname;

    /** @var string */
    private $email;

    /** @var string */
    private $guid;

    /** @var string|null */
    private $uniqueId;

    /** @var string */
    private $ou;

    /** @var string[] */
    private $groups;

    /**
     * @return string
     */
    public function getUsername(): string {
        return $this->username;
    }

    /**
     * @param string $username
     * @return ActiveDirectoryUserInformation
     */
    public function setUsername(string $username): ActiveDirectoryUserInformation {
        $this->username = $username;
        return $this;
    }

    /**
     * @return string
     */
    public function getUserPrincipalName(): string {
        return $this->userPrincipalName;
    }

    /**
     * @param string $userPrincipalName
     * @return ActiveDirectoryUserInformation
     */
    public function setUserPrincipalName(string $userPrincipalName): ActiveDirectoryUserInformation {
        $this->userPrincipalName = $userPrincipalName;
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
     * @return ActiveDirectoryUserInformation
     */
    public function setFirstname(?string $firstname): ActiveDirectoryUserInformation {
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
     * @return ActiveDirectoryUserInformation
     */
    public function setLastname(?string $lastname): ActiveDirectoryUserInformation {
        $this->lastname = $lastname;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string {
        return $this->email;
    }

    /**
     * @param string $email
     * @return ActiveDirectoryUserInformation
     */
    public function setEmail(string $email): ActiveDirectoryUserInformation {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getGuid(): string {
        return $this->guid;
    }

    /**
     * @param string $guid
     * @return ActiveDirectoryUserInformation
     */
    public function setGuid(string $guid): ActiveDirectoryUserInformation {
        $this->guid = $guid;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUniqueId(): ?string {
        return $this->uniqueId;
    }

    /**
     * @param string|null $uniqueId
     * @return ActiveDirectoryUserInformation
     */
    public function setUniqueId(?string $uniqueId): ActiveDirectoryUserInformation {
        $this->uniqueId = $uniqueId;
        return $this;
    }

    /**
     * @return string
     */
    public function getOu(): string {
        return $this->ou;
    }

    /**
     * @param string $ou
     * @return ActiveDirectoryUserInformation
     */
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
     * @return ActiveDirectoryUserInformation
     */
    public function setGroups(array $groups): ActiveDirectoryUserInformation {
        $this->groups = $groups;
        return $this;
    }
}