<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class ActiveDirectoryRoleSyncOption implements ActiveDirectorySyncOptionInterface {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @ORM\OrderBy()
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
     * @ORM\Column(type="string", nullable=false)
     */
    private $sourceType = ActiveDirectorySyncSourceType::OU;

    /**
     * @ORM\ManyToOne(targetEntity="UserRole")
     * @ORM\JoinColumn()
     */
    private $userRole;

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
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
     * @return string
     */
    public function getSourceType(): string {
        return $this->sourceType;
    }

    /**
     * @param string $sourceType
     * @return ActiveDirectoryRoleSyncOption
     */
    public function setSourceType(string $sourceType): ActiveDirectoryRoleSyncOption {
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