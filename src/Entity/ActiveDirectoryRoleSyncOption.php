<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class ActiveDirectoryRoleSyncOption implements ActiveDirectorySyncOptionInterface {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotBlank()
     */
    private $source;

    /**
     * @ORM\Column(type="ad_source_type", nullable=false)
     */
    private $sourceType;

    /**
     * @ORM\ManyToOne(targetEntity="UserRole")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $userRole;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->sourceType = ActiveDirectorySyncSourceType::Ou();
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     * @return ActiveDirectoryRoleSyncOption
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @param string $description
     * @return ActiveDirectoryRoleSyncOption
     */
    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getSource() {
        return $this->source;
    }

    /**
     * @param string $source
     * @return ActiveDirectoryRoleSyncOption
     */
    public function setSource($source) {
        $this->source = $source;
        return $this;
    }

    /**
     * @return ActiveDirectorySyncSourceType
     */
    public function getSourceType(): ActiveDirectorySyncSourceType {
        return $this->sourceType;
    }

    /**
     * @param ActiveDirectorySyncSourceType $sourceType
     * @return ActiveDirectoryRoleSyncOption
     */
    public function setSourceType(ActiveDirectorySyncSourceType $sourceType): ActiveDirectoryRoleSyncOption {
        $this->sourceType = $sourceType;
        return $this;
    }

    /**
     * @return UserRole
     */
    public function getUserRole() {
        return $this->userRole;
    }

    /**
     * @param UserRole $userRole
     * @return ActiveDirectoryRoleSyncOption
     */
    public function setUserRole(UserRole $userRole) {
        $this->userRole = $userRole;
        return $this;
    }
}