<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class UserRole {

    use IdTrait;
    use UuidTrait;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    private string $name;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    private string $description;

    /**
     * @var Collection<User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'userRoles')]
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
     * @var Collection<ServiceAttributeUserRoleValue>
     */
    #[ORM\OneToMany(targetEntity: ServiceAttributeUserRoleValue::class, mappedBy: 'userRole')]
    #[Serializer\Exclude]
    private Collection $attributes;

    #[ORM\Column(name: 'priority', type: 'integer')]
    #[Gedmo\SortablePosition]
    private int $priority = 0;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->users = new ArrayCollection();
        $this->enabledServices = new ArrayCollection();
        $this->attributes = new ArrayCollection();
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): self {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function setDescription(string $description): self {
        $this->description = $description;
        return $this;
    }

    public function getUsers(): Collection {
        return $this->users;
    }

    public function addUser(User $user): void {
        $this->users->add($user);
    }

    public function removeUser(User $user): void {
        $this->users->removeElement($user);
    }

    public function getEnabledServices(): Collection {
        return $this->enabledServices;
    }

    public function getAttributes(): Collection {
        return $this->attributes;
    }

    public function addEnabledService(ServiceProvider $serviceProvider): void {
        $this->enabledServices->add($serviceProvider);
    }

    public function removeEnabledService(ServiceProvider $serviceProvider): void {
        $this->enabledServices->removeElement($serviceProvider);
    }

    public function getPriority(): int {
        return $this->priority;
    }

    public function setPriority(int $priority): UserRole {
        $this->priority = $priority;
        return $this;
    }
}