<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity]
class ActiveDirectoryUser extends User {

    #[Serializer\Exclude]
    #[ORM\Column(type: 'string')]
    private ?string $userPrincipalName = null;

    #[Serializer\Exclude]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?UuidInterface $objectGuid = null;

    #[Serializer\Exclude]
    #[ORM\Column(type: 'string')]
    private ?string $ou = null;

    /**
     * @var string[]
     */
    #[Serializer\Exclude]
    #[ORM\Column(type: 'json')]
    private array $groups = [ ];

    public function getUserPrincipalName(): string {
        return $this->userPrincipalName;
    }

    /**
     * @return ActiveDirectoryUser
     */
    public function setUserPrincipalName(string $userPrincipalName) {
        $this->userPrincipalName = $userPrincipalName;
        return $this;
    }

    public function getObjectGuid(): UuidInterface {
        return $this->objectGuid;
    }

    public function setObjectGuid(UuidInterface $objectGuid): ActiveDirectoryUser {
        $this->objectGuid = $objectGuid;
        return $this;
    }

    public function getOu(): string {
        return $this->ou;
    }

    public function setOu(string $ou): ActiveDirectoryUser {
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
    public function setGroups(array $groups): ActiveDirectoryUser {
        $this->groups = $groups;
        return $this;
    }
}