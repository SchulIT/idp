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
    private $name;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    private $description;

    /**
     * @Serializer\Exclude()
     */
    #[ORM\JoinTable]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: 'User', mappedBy: 'userRoles')]
    private $users;

    /**
     * @Serializer\Exclude()
     */
    #[ORM\JoinTable]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: 'ServiceProvider')]
    private $enabledServices;

    /**
     * @Serializer\Exclude()
     */
    #[ORM\OneToMany(targetEntity: 'ServiceAttributeUserRoleValue', mappedBy: 'userRole')]
    private $attributes;

    /**
     * @Gedmo\SortablePosition()
     */
    #[ORM\Column(name: 'priority', type: 'integer')]
    private int $priority = 0;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->users = new ArrayCollection();
        $this->enabledServices = new ArrayCollection();
        $this->attributes = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     * @return UserRole
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
     * @return UserRole
     */
    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }

    public function getUsers(): Collection {
        return $this->users;
    }

    public function addUser(User $user) {
        $this->users->add($user);
    }

    public function removeUser(User $user) {
        $this->users->removeElement($user);
    }

    public function getEnabledServices(): Collection {
        return $this->enabledServices;
    }

    public function getAttributes(): Collection {
        return $this->attributes;
    }

    public function addEnabledService(ServiceProvider $serviceProvider) {
        $this->enabledServices->add($serviceProvider);
    }

    public function removeEnabledService(ServiceProvider $serviceProvider) {
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