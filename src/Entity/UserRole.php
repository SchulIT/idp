<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class UserRole {
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @ORM\OrderBy()
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="userRoles")
     * @ORM\JoinTable(
     *   joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *   inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
     * )
     * @Serializer\Exclude()
     */
    private $users;

    /**
     * @ORM\ManyToMany(targetEntity="ServiceProvider")
     * @ORM\JoinTable(
     *   joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *   inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
     * )
     * @Serializer\Exclude()
     */
    private $enabledServices;

    /**
     * @ORM\OneToMany(targetEntity="ServiceAttributeUserRoleValue", mappedBy="userRole")
     * @Serializer\Exclude()
     */
    private $attributes;

    public function __construct() {
        $this->users = new ArrayCollection();
        $this->enabledServices = new ArrayCollection();
        $this->attributes = new ArrayCollection();
    }

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

    /**
     * @return Collection
     */
    public function getUsers(): Collection {
        return $this->users;
    }

    /**
     * @param User $user
     */
    public function addUser(User $user) {
        $this->users->add($user);
    }

    /**
     * @param User $user
     */
    public function removeUser(User $user) {
        $this->users->removeElement($user);
    }

    /**
     * @return Collection
     */
    public function getEnabledServices(): Collection {
        return $this->enabledServices;
    }

    /**
     * @return Collection
     */
    public function getAttributes(): Collection {
        return $this->attributes;
    }

    /**
     * @param ServiceProvider $serviceProvider
     */
    public function addEnabledService(ServiceProvider $serviceProvider) {
        $this->enabledServices->add($serviceProvider);
    }

    /**
     * @param ServiceProvider $serviceProvider
     */
    public function removeEnabledService(ServiceProvider $serviceProvider) {
        $this->enabledServices->removeElement($serviceProvider);
    }
}