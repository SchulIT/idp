<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[UniqueEntity(fields: ['grade'])]
#[UniqueEntity(fields: ['source', 'sourceType'])]
class ActiveDirectoryGradeSyncOption implements ActiveDirectorySyncOptionInterface {

    use IdTrait;
    use UuidTrait;

    #[ORM\Column(type: 'string', length: 32)]
    #[Assert\Length(max: 32)]
    #[Assert\NotBlank]
    private string $grade;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    private string $source;

    #[ORM\Column(type: 'string', enumType: ActiveDirectorySyncSourceType::class)]
    private ActiveDirectorySyncSourceType $sourceType;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->sourceType = ActiveDirectorySyncSourceType::Ou;
    }

    public function getGrade(): string {
        return $this->grade;
    }

    public function setGrade(string $grade): self {
        $this->grade = $grade;
        return $this;
    }

    public function getSource(): string {
        return $this->source;
    }

    public function setSource(string $source): self {
        $this->source = $source;
        return $this;
    }

    public function getSourceType(): ActiveDirectorySyncSourceType {
        return $this->sourceType;
    }

    public function setSourceType(ActiveDirectorySyncSourceType $sourceType): self {
        $this->sourceType = $sourceType;
        return $this;
    }
}