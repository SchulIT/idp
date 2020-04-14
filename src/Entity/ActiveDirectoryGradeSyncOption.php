<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @UniqueEntity(fields={"grade"})
 * @UniqueEntity(fields={"source", "sourceType"})
 */
class ActiveDirectoryGradeSyncOption implements ActiveDirectorySyncOptionInterface {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\Column(type="string", length=32)
     * @Assert\Length(max="32")
     * @Assert\NotBlank()
     */
    private $grade;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $source;

    /**
     * @ORM\Column(type="string")
     */
    private $sourceType = ActiveDirectorySyncSourceType::OU;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    /**
     * @return string
     */
    public function getGrade() {
        return $this->grade;
    }

    /**
     * @param string $grade
     * @return ActiveDirectoryGradeSyncOption
     */
    public function setGrade($grade) {
        $this->grade = $grade;
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
     * @return ActiveDirectoryGradeSyncOption
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
     * @return ActiveDirectoryGradeSyncOption
     */
    public function setSourceType($sourceType) {
        $this->sourceType = $sourceType;
        return $this;
    }
}