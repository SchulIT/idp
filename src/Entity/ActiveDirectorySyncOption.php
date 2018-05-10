<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"})
 * @UniqueEntity(fields={"source"})
 */
class ActiveDirectorySyncOption implements ActiveDirectorySyncOptionInterface {

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
     * @ORM\Column(type="string", unique=true, name="source_group")
     * @Assert\NotBlank()
     */
    private $source;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $sourceType = ActiveDirectorySyncSourceType::OU;

    /**
     * @ORM\ManyToOne(targetEntity="UserType", inversedBy="syncOptions")
     * @ORM\JoinColumn(name="user_type", referencedColumnName="id")
     */
    private $userType;

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     * @return ActiveDirectorySyncOption
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return ActiveDirectorySyncOption
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
     * @return ActiveDirectorySyncOption
     */
    public function setSource($source) {
        $this->source = $source;
        return $this;
    }

    /**
     * @return string
     */
    public function getSourceType() {
        return $this->sourceType;
    }

    /**
     * @param string $sourceType
     * @return ActiveDirectorySyncOption
     */
    public function setSourceType($sourceType) {
        $this->sourceType = $sourceType;
        return $this;
    }

    /**
     * @return UserType
     */
    public function getUserType() {
        return $this->userType;
    }

    /**
     * @param UserType $userType
     * @return ActiveDirectorySyncOption
     */
    public function setUserType($userType) {
        $this->userType = $userType;
        return $this;
    }
}