<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity()
 */
class ActiveDirectoryUser extends User {
    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $userPrincipalName;


    /**
     * @ORM\Column(type="uuid", unique=true)
     * @var UuidInterface
     */
    private $objectGuid;

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
}