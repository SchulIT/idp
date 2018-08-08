<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"})
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
     *   joinColumns={@ORM\JoinColumn(name="role", referencedColumnName="id", onDelete="CASCADE")},
     *   inverseJoinColumns={@ORM\JoinColumn(name="user", referencedColumnName="id", onDelete="CASCADE")}
     * )
     * @Serializer\Exclude()
     */
    private $users;

    /**
     * @ORM\ManyToMany(targetEntity="ServiceProvider")
     * @ORM\JoinTable(
     *   joinColumns={@ORM\JoinColumn(name="role", referencedColumnName="id", onDelete="CASCADE")},
     *   inverseJoinColumns={@ORM\JoinColumn(name="service", referencedColumnName="id", onDelete="CASCADE")}
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
     * @return ArrayCollection
     */
    public function getUsers() {
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
     * @return ArrayCollection
     */
    public function getEnabledServices() {
        return $this->enabledServices;
    }

    /**
     * @return ArrayCollection
     */
    public function getAttributes() {
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