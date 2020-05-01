<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity()
 */
class ActiveDirectoryUser extends User {

    /**
     * @ORM\Column(type="string")
     * @Serializer\Exclude()
     * @var string
     */
    private $userPrincipalName;


    /**
     * @ORM\Column(type="uuid", unique=true)
     * @Serializer\Exclude()
     * @var UuidInterface
     */
    private $objectGuid;

    /**
     * @ORM\Column(type="string")
     * @Serializer\Exclude()
     * @var string
     */
    private $ou;

    /**
     * @ORM\Column(type="json")
     * @Serializer\Exclude()
     * @var string[]
     */
    private $groups = [ ];

    /**
     * @return string
     */
    public function getUserPrincipalName(): string {
        return $this->userPrincipalName;
    }

    /**
     * @param string $userPrincipalName
     * @return ActiveDirectoryUser
     */
    public function setUserPrincipalName(string $userPrincipalName) {
        $this->userPrincipalName = $userPrincipalName;
        return $this;
    }

    /**
     * @return UuidInterface
     */
    public function getObjectGuid(): UuidInterface {
        return $this->objectGuid;
    }

    /**
     * @param UuidInterface $objectGuid
     * @return ActiveDirectoryUser
     */
    public function setObjectGuid(UuidInterface $objectGuid): ActiveDirectoryUser {
        $this->objectGuid = $objectGuid;
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
     * @return ActiveDirectoryUser
     */
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
     * @return ActiveDirectoryUser
     */
    public function setGroups(array $groups): ActiveDirectoryUser {
        $this->groups = $groups;
        return $this;
    }
}