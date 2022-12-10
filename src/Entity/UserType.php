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
    private $name;

    #[ORM\Column(type: 'string', unique: true)]
    #[Assert\NotBlank]
    private $alias;

    /**
     * @var string[]
     * @Serializer\Type("array<string>")
     */
    #[ORM\Column(type: 'json')]
    #[Assert\Count(min: 1)]
    private ?array $eduPerson = null;

    /**
     * @Serializer\Exclude()
     */
    #[ORM\OneToMany(mappedBy: 'type', targetEntity: User::class)]
    private $users;

    /**
     * @Serializer\Exclude()
     */
    #[ORM\JoinTable]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: ServiceProvider::class)]
    private $enabledServices;

    /**
     * @Serializer\Exclude()
     */
    #[ORM\OneToMany(mappedBy: 'userType', targetEntity: ServiceAttributeUserTypeValue::class)]
    private $attributes;

    /**
     * @Serializer\Exclude()
     */
    #[ORM\OneToMany(targetEntity: 'ActiveDirectorySyncOption', mappedBy: 'userType')]
    private $syncOptions;

    /**
     * @Serializer\Exclude()
     */
    #[ORM\Column(type: 'boolean')]
    private bool $canChangeName = true;

    /**
     * @Serializer\Exclude()
     */
    #[ORM\Column(type: 'boolean')]
    private bool $canChangeEmail = true;

    /**
     * @Serializer\Exclude()
     */
    #[ORM\Column(type: 'boolean')]
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

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     * @return UserType
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getAlias() {
        return $this->alias;
    }

    /**
     * @param string $alias
     * @return UserType
     */
    public function setAlias($alias) {
        $this->alias = $alias;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getEduPerson() {
        return $this->eduPerson;
    }

    /**
     * @param string[] $eduPerson
     * @return UserType
     */
    public function setEduPerson(array $eduPerson) {
        sort($eduPerson);

        $this->eduPerson = $eduPerson;
        return $this;
    }

    public function addUser(User $user) {
        $this->users->add($user);
    }

    public function removeUser(User $user) {
        $this->users->removeElement($user);
    }

    public function getUsers(): Collection {
        return $this->users;
    }

    public function addEnabledService(ServiceProvider $serviceProvider) {
        $this->enabledServices->add($serviceProvider);
    }

    public function removeEnabledService(ServiceProvider $serviceProvider) {
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

    /**
     * @return bool
     */
    public function canChangeName() {
        return $this->canChangeName;
    }

    /**
     * @param bool $canChangeName
     * @return UserType
     */
    public function setCanChangeName($canChangeName) {
        $this->canChangeName = $canChangeName;
        return $this;
    }

    /**
     * @return bool
     */
    public function canChangeEmail() {
        return $this->canChangeEmail;
    }

    /**
     * @param bool $canChangeEmail
     * @return UserType
     */
    public function setCanChangeEmail($canChangeEmail) {
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