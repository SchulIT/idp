<?php

namespace App\Request;

use App\Validator\UniqueUsername;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class ActiveDirectoryUserRequest {
    /**
     * @Serializer\SerializedName("object_guid")
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    private string $objectGuid;

    /**
     * @Serializer\SerializedName("sam_account_name")
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank]
    private string $samAccountName;

    /**
     * @Serializer\SerializedName("user_principal_name")
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank]
    private string $userPrincipalName;

    /**
     * @Serializer\SerializedName("firstname")
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank]
    private string $firstname;

    /**
     * @Serializer\SerializedName("lastname")
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank]
    private string $lastname;

    /**
     * @Serializer\SerializedName("email")
     * @Serializer\Type("string")
     */
    #[Assert\Email]
    #[Assert\NotBlank]
    private string $email;

    /**
     * @Serializer\SerializedName("ou")
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank]
    private string $ou;

    /**
     * @Serializer\SerializedName("groups")
     * @Serializer\Type("array<string>")
     * @var string[]
     */
    private array $groups;

    /**
     * @Serializer\SerializedName("unique_id")
     * @Serializer\Type("string")
     */
    private ?string $uniqueId = null;

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

    public function getUniqueId(): ?string {
        return $this->uniqueId;
    }
}