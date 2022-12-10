<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[UniqueEntity(fields: ['source'])]
class ActiveDirectorySyncOption implements ActiveDirectorySyncOptionInterface {

    use IdTrait;
    use UuidTrait;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    private $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private $description;

    #[ORM\Column(type: 'string', unique: true)]
    #[Assert\NotBlank]
    private $source;

    #[ORM\Column(type: 'string', nullable: false, enumType: ActiveDirectorySyncSourceType::class)]
    private $sourceType;

    #[ORM\ManyToOne(targetEntity: 'UserType', inversedBy: 'syncOptions')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private $userType;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->sourceType = ActiveDirectorySyncSourceType::Ou;
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

    public function getSourceType(): ActiveDirectorySyncSourceType {
        return $this->sourceType;
    }

    /**
     * @return ActiveDirectorySyncOption
     */
    public function setSourceType(ActiveDirectorySyncSourceType $sourceType) {
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