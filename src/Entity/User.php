<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use JMS\Serializer\Annotation as Serializer;
use Ramsey\Uuid\Uuid;
use Scheb\TwoFactorBundle\Model\Google\TwoFactorInterface;
use Swagger\Annotations as SWG;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="class", type="string")
 * @ORM\DiscriminatorMap({"user" = "User", "ad" = "ActiveDirectoryUser"})
 * @UniqueEntity(fields={"username"})
 * @Serializer\Discriminator(disabled=true)
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=true)
 */
class User implements UserInterface, TwoFactorInterface {

    use IdTrait;
    use UuidTrait;
    use SoftDeleteableEntity;

    /**
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotBlank()
     * @Assert\Email(mode="html5")
     * @ORM\OrderBy()
     * @Assert\Length(max="128", min="4")
     */
    private $username;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank(allowNull=true)
     * @var string|null
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank(allowNull=true)
     * @var string|null
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=62, nullable=true)
     * @Serializer\Exclude()
     */
    private $password;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank(allowNull=true)
     * @Assert\Length(max="191")
     * @Assert\Email()
     * @var string|null
     */
    private $email;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string|null
     */
    private $grade;

    /**
     * @ORM\ManyToOne(targetEntity="UserType", inversedBy="users")
     * @ORM\JoinColumn(onDelete="SET NULL")
     * @Serializer\ReadOnly()
     * @Serializer\Accessor(getter="getTypeString")
     * @Serializer\Type("string")
     * @SWG\Property(description="UUID of the usertype")
     * @Assert\NotNull()
     * @var UserType|null
     */
    private $type;

    /**
     * @ORM\Column(type="json")
     * @Serializer\Exclude()
     */
    private $roles = [ 'ROLE_USER' ];

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $externalId;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive = true;

    /**
     * @ORM\Column(type="boolean")
     * @Serializer\Exclude()
     * @var bool
     */
    private $isEmailConfirmationPending = false;

    /**
     * @ORM\ManyToMany(targetEntity="ServiceProvider")
     * @ORM\JoinTable(
     *  joinColumns={@ORM\JoinColumn(onDelete="CASCADE")},
     *  inverseJoinColumns={@ORM\JoinColumn(onDelete="CASCADE")}
     * )
     * @Serializer\Exclude()
     */
    private $enabledServices;

    /**
     * @ORM\OneToMany(targetEntity="ServiceAttributeValue", mappedBy="user")
     * @Serializer\Exclude()
     */
    private $attributes;

    /**
     * @ORM\ManyToMany(targetEntity="UserRole", inversedBy="users")
     * @Serializer\Exclude()
     */
    private $userRoles;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Serializer\Exclude()
     */
    private $googleAuthenticatorSecret;

    /**
     * @ORM\Column(type="json")
     * @Serializer\Exclude()
     */
    private $backupCodes = [ ];

    /**
     * @ORM\Column(type="integer", name="trusted_version")
     * @Serializer\Exclude()
     */
    private $trustedVersion = 0;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     * @Serializer\Exclude()
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update", field={"firstname", "lastname", "email", "type", "userRoles"})
     * @Serializer\Exclude()
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $enabledFrom;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $enabledUntil;

    /**
     * @ORM\Column(type="json")
     * @Serializer\Exclude()
     * @var array
     */
    private $data = [ ];

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var DateTime|null
     */
    private $privacyPolicyConfirmedAt = null;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $isProvisioned = true;

    /**
     * @ORM\Column(type="boolean")
     * @var bool
     */
    private $mustChangePassword = false;

    /**
     * @ORM\Column(type="boolean", options={"default": 1 })
     * @var bool
     */
    private $canChangePassword = true;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="parents")
     * @ORM\JoinTable(name="user_links",
     *     joinColumns={@ORM\JoinColumn(name="source_user_id", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="target_user_id", onDelete="CASCADE")}
     * )
     * @var Collection<User>
     */
    private $linkedStudents;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="linkedStudents")
     * @var Collection<User>
     */
    private $parents;

    public function __construct() {
        $this->uuid = Uuid::uuid4();

        $this->enabledServices = new ArrayCollection();
        $this->attributes = new ArrayCollection();
        $this->userRoles = new ArrayCollection();
        $this->linkedStudents = new ArrayCollection();
        $this->parents = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * @param string $username
     * @return User
     */
    public function setUsername($username) {
        $this->username = mb_strtolower($username);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFirstname() {
        return $this->firstname;
    }

    /**
     * @param string|null $firstname
     * @return User
     */
    public function setFirstname($firstname) {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastname() {
        return $this->lastname;
    }

    /**
     * @param string|null $lastname
     * @return User
     */
    public function setLastname($lastname) {
        $this->lastname = $lastname;
        return $this;
    }

    /**
     * @param string $password
     * @return User
     */
    public function setPassword($password) {
        $this->password = $password;
        return $this;
    }

    /**
     * @param string|null $email
     * @return User
     */
    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @return string|null
     */
    public function getGrade() {
        return $this->grade;
    }

    /**
     * @param string $grade
     * @return User
     */
    public function setGrade($grade) {
        $this->grade = $grade;
        return $this;
    }

    /**
     * @return UserType
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @param UserType $userType
     * @return User
     */
    public function setType(UserType $userType) {
        $this->type = $userType;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getExternalId() {
        if($this->linkedStudents->count() > 0) {
            return implode(',', $this->linkedStudents->map(function(User $user) {
                return $user->getExternalId();
            })->toArray());
        }

        return $this->externalId;
    }

    /**
     * @param string|null $externalId
     * @return User
     */
    public function setExternalId($externalId) {
        $this->externalId = $externalId;
        return $this;
    }

    /**
     * @return bool
     */
    public function isActive() {
        return $this->isActive;
    }

    /**
     * @param bool $active
     * @return User
     */
    public function setIsActive($active) {
        $this->isActive = $active;
        return $this;
    }

    /**
     * @return bool
     */
    public function isEmailConfirmationPending(): bool {
        return $this->isEmailConfirmationPending;
    }

    /**
     * @param bool $isEmailConfirmationPending
     * @return User
     */
    public function setIsEmailConfirmationPending(bool $isEmailConfirmationPending): User {
        $this->isEmailConfirmationPending = $isEmailConfirmationPending;
        return $this;
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
     * @return Collection
     */
    public function getUserRoles(): Collection {
        return $this->userRoles;
    }

    /**
     * @param UserRole $role
     */
    public function addUserRole(UserRole $role) {
        $this->userRoles->add($role);
    }

    /**
     * @param UserRole $role
     */
    public function removeUserRole(UserRole $role) {
        $this->userRoles->removeElement($role);
    }

    /**
     * @return string[]
     */
    public function getRoles() {
        return $this->roles;
    }

    /**
     * @param string[] $roles
     * @return User
     */
    public function setRoles(array $roles) {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * @return null
     */
    public function getSalt() {
        return null;
    }

    /**
     * @return null
     */
    public function eraseCredentials() {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getGoogleAuthenticatorSecret(): string {
        if($this->googleAuthenticatorSecret === null) {
            return ''; // dirty hack
        }

        return $this->googleAuthenticatorSecret;
    }

    /**
     * @inheritDoc
     */
    public function setGoogleAuthenticatorSecret(?string $googleAuthenticatorSecret): void {
        $this->googleAuthenticatorSecret = $googleAuthenticatorSecret;
    }

    /**
     * @return string[]
     */
    public function getBackupCodes() {
        return $this->backupCodes;
    }

    public function emptyBackupCodes() {
        $this->backupCodes = [ ];
    }

    /**
     * @param string[] $backupCodes
     * @return User
     */
    public function setBackupCodes(array $backupCodes) {
        $this->backupCodes = $backupCodes;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isBackupCode(string $code): bool {
        return in_array($code, $this->backupCodes);
    }

    /**
     * @inheritDoc
     */
    public function invalidateBackupCode(string $code): void {
        $key = array_search($code, $this->backupCodes);
        if ($key !== false){
            unset($this->backupCodes[$key]);
        }
    }

    /**
     * @return bool
     */
    public function isGoogleAuthenticatorEnabled(): bool {
        return $this->googleAuthenticatorSecret !== null;
    }

    /**
     * @return string
     */
    public function getGoogleAuthenticatorUsername(): string {
        return $this->getUsername();
    }

    /**
     * @return int
     */
    public function getTrustedTokenVersion(): int {
        return $this->trustedVersion;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime {
        return $this->createdAt;
    }

    /**
     * @return \DateTime|null
     */
    public function getUpdatedAt(): ?\DateTime {
        return $this->updatedAt;
    }

    /**
     * @return \DateTime|null
     */
    public function getEnabledFrom(): ?\DateTime {
        return $this->enabledFrom;
    }

    /**
     * @param \DateTime|null $enabledFrom
     * @return User
     */
    public function setEnabledFrom(?\DateTime $enabledFrom): User {
        $this->enabledFrom = $enabledFrom;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getEnabledUntil(): ?\DateTime {
        return $this->enabledUntil;
    }

    /**
     * @param \DateTime|null $enabledUntil
     * @return User
     */
    public function setEnabledUntil(?\DateTime$enabledUntil): User {
        $this->enabledUntil = $enabledUntil;
        return $this;
    }

    public function getData(string $key, $default = null) {
        return $this->data[$key] ?? $default;
    }

    public function setData(string $key, $value): void {
        $this->data[$key] = $value;
    }

    /**
     * @return DateTime|null
     */
    public function getPrivacyPolicyConfirmedAt(): ?DateTime {
        return $this->privacyPolicyConfirmedAt;
    }

    /**
     * @param DateTime|null $privacyPolicyConfirmedAt
     * @return User
     */
    public function setPrivacyPolicyConfirmedAt(?DateTime $privacyPolicyConfirmedAt): User {
        $this->privacyPolicyConfirmedAt = $privacyPolicyConfirmedAt;
        return $this;
    }

    /**
     * @return bool
     */
    public function isProvisioned(): bool {
        return $this->isProvisioned;
    }

    /**
     * @param bool $isProvisioned
     * @return User
     */
    public function setIsProvisioned(bool $isProvisioned): User {
        $this->isProvisioned = $isProvisioned;
        return $this;
    }

    /**
     * @return bool
     */
    public function isMustChangePassword(): bool {
        return $this->mustChangePassword;
    }

    /**
     * @param bool $mustChangePassword
     * @return User
     */
    public function setMustChangePassword(bool $mustChangePassword): User {
        $this->mustChangePassword = $mustChangePassword;
        return $this;
    }

    /**
     * @return bool
     */
    public function canChangePassword(): bool {
        return $this->canChangePassword;
    }

    /**
     * @param bool $canChangePassword
     * @return User
     */
    public function setCanChangePassword(bool $canChangePassword): User {
        $this->canChangePassword = $canChangePassword;
        return $this;
    }

    public function addLinkedStudent(User $user): void {
        $this->linkedStudents->add($user);
    }

    public function removeLinkedStudent(User $user): void {
        $this->linkedStudents->removeElement($user);
    }

    /**
     * @return Collection
     */
    public function getLinkedStudents(): Collection {
        return $this->linkedStudents;
    }

    /**
     * @return Collection
     */
    public function getParents(): Collection {
        return $this->parents;
    }

    public function getTypeString(): string {
        return (string)$this->getType()->getUuid();
    }
}