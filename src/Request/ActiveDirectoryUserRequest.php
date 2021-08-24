<?php

namespace App\Request;

use App\Validator\UniqueUsername;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class ActiveDirectoryUserRequest {
    /**
     * @Serializer\SerializedName("object_guid")
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @Assert\Uuid()
     * @var string
     */
    private $objectGuid;

    /**
     * @Serializer\SerializedName("sam_account_name")
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @var string
     */
    private $samAccountName;

    /**
     * @Serializer\SerializedName("user_principal_name")
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @var string
     */
    private $userPrincipalName;

    /**
     * @Serializer\SerializedName("firstname")
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @var string
     */
    private $firstname;

    /**
     * @Serializer\SerializedName("lastname")
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @var string
     */
    private $lastname;

    /**
     * @Serializer\SerializedName("email")
     * @Serializer\Type("string")
     * @Assert\Email()
     * @Assert\NotBlank()
     * @var string
     */
    private $email;

    /**
     * @Serializer\SerializedName("ou")
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @var string
     */
    private $ou;

    /**
     * @Serializer\SerializedName("groups")
     * @Serializer\Type("array<string>")
     * @var string[]
     */
    private $groups;

    /**
     * @Serializer\SerializedName("unique_id")
     * @Serializer\Type("string")
     * @var string|null
     */
    private $uniqueId;

    /**
     * @return string|null
     */
    public function getObjectGuid(): ?string {
        return $this->objectGuid;
    }

    /**
     * @return string|null
     */
    public function getSamAccountName(): ?string {
        return $this->samAccountName;
    }

    /**
     * @return string|null
     */
    public function getUserPrincipalName(): ?string {
        return $this->userPrincipalName;
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
    public function getOu(): ?string {
        return $this->ou;
    }

    /**
     * @return string[]
     */
    public function getGroups(): array {
        return $this->groups;
    }

    /**
     * @return string|null
     */
    public function getUniqueId(): ?string {
        return $this->uniqueId;
    }
}