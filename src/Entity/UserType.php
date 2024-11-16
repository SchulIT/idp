<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[UniqueEntity(fields: ['alias'])]
class UserType {

    use IdTrait;
    use UuidTrait;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    private string $name;

    #[ORM\Column(type: 'string', unique: true)]
    #[Assert\NotBlank]
    private string $alias;

    /**
     * @var string[]
     */
    #[ORM\Column(type: 'json')]
    #[Assert\Count(min: 1)]
    #[Serializer\Type('array<string>')]
    private array $eduPerson = [];

    /**
     * @var Collection<User>
     */
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'type')]
    #[Serializer\Exclude]
    private Collection $users;

    /**
     * @var Collection<ServiceProvider>
     */
    #[ORM\JoinTable]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: ServiceProvider::class)]
    #[Serializer\Exclude]
    private Collection $enabledServices;

    /**
     * @var Collection<ServiceAttributeUserTypeValue>
     */
    #[ORM\OneToMany(targetEntity: ServiceAttributeUserTypeValue::class, mappedBy: 'userType')]
    #[Serializer\Exclude]
    private Collection $attributes;

    /**
     * @var Collection<ActiveDirectorySyncOption>
     */
    #[ORM\OneToMany(targetEntity: ActiveDirectorySyncOption::class, mappedBy: 'userType')]
    #[Serializer\Exclude]
    private Collection $syncOptions;

    #[ORM\Column(type: 'boolean')]
    #[Serializer\Exclude]
    private bool $canChangeName = true;

    #[ORM\Column(type: 'boolean')]
    #[Serializer\Exclude]
    private bool $canChangeEmail = true;

    #[ORM\Column(type: 'boolean')]
    #[Serializer\Exclude]
    private bool $canLinkStudents = false;

    #[ORM\Column(type: 'boolean')]
    private bool $isBuiltIn = false;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $icon = null;

    public function __construct() {
        $this->uuid = Uuid::uuid4();

        $this->enabledServices = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->syncOptions = new ArrayCollection();
        $this->attributes = new ArrayCollection();
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): self {
        $this->name = $name;
        return $this;
    }

    public function getAlias(): string {
        return $this->alias;
    }

    public function setAlias(string $alias): self {
        $this->alias = $alias;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getEduPerson(): array {
        return $this->eduPerson;
    }

    /**
     * @param string[] $eduPerson
     * @return UserType
     */
    public function setEduPerson(array $eduPerson): self {
        sort($eduPerson);

        $this->eduPerson = $eduPerson;
        return $this;
    }

    public function addUser(User $user): void {
        $this->users->add($user);
    }

    public function removeUser(User $user): void {
        $this->users->removeElement($user);
    }

    public function getUsers(): Collection {
        return $this->users;
    }

    public function addEnabledService(ServiceProvider $serviceProvider): void {
        $this->enabledServices->add($serviceProvider);
    }

    public function removeEnabledService(ServiceProvider $serviceProvider): void {
        $this->enabledServices->removeElement($serviceProvider);
    }

    public function getEnabledServices(): Collection {
        return $this->enabledServices;
    }

    public function getAttributes(): Collection {
        return $this->attributes;
    }

    public function getSyncOptions(): Collection {
        return $this->syncOptions;
    }

    public function canChangeName(): bool {
        return $this->canChangeName;
    }

    public function setCanChangeName(bool $canChangeName): self {
        $this->canChangeName = $canChangeName;
        return $this;
    }

    public function canChangeEmail(): bool {
        return $this->canChangeEmail;
    }


    public function setCanChangeEmail(bool $canChangeEmail): self {
        $this->canChangeEmail = $canChangeEmail;
        return $this;
    }

    public function isCanLinkStudents(): bool {
        return $this->canLinkStudents;
    }

    public function setCanLinkStudents(bool $canLinkStudents): UserType {
        $this->canLinkStudents = $canLinkStudents;
        return $this;
    }

    public function isBuiltIn(): bool {
        return $this->isBuiltIn;
    }

    public function setIsBuiltIn(bool $isBuiltIn): UserType {
        $this->isBuiltIn = $isBuiltIn;
        return $this;
    }

    public function getIcon(): ?string {
        return $this->icon;
    }

    public function setIcon(?string $icon): UserType {
        $this->icon = $icon;
        return $this;
    }
}