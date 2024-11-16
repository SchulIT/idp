<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class ActiveDirectoryRoleSyncOption implements ActiveDirectorySyncOptionInterface {

    use IdTrait;
    use UuidTrait;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description;

    #[ORM\Column(type: 'string', unique: true)]
    #[Assert\NotBlank]
    private string $source;

    #[ORM\Column(type: 'string', nullable: false, enumType: ActiveDirectorySyncSourceType::class)]
    private ActiveDirectorySyncSourceType $sourceType;

    #[ORM\ManyToOne(targetEntity: UserRole::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?UserRole $userRole;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->sourceType = ActiveDirectorySyncSourceType::Ou;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): self {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function setDescription(?string $description): self {
        $this->description = $description;
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

    public function setSourceType(ActiveDirectorySyncSourceType $sourceType): ActiveDirectoryRoleSyncOption {
        $this->sourceType = $sourceType;
        return $this;
    }

    public function getUserRole(): ?UserRole {
        return $this->userRole;
    }

    public function setUserRole(UserRole $userRole): self {
        $this->userRole = $userRole;
        return $this;
    }
}