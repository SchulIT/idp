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
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'class', type: 'string')]
#[ORM\DiscriminatorMap(['user' => 'User', 'ad' => 'ActiveDirectoryUser'])]
#[UniqueEntity(fields: ['username'])]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
#[Serializer\Discriminator(disabled: true)]
class User implements UserInterface, PasswordAuthenticatedUserInterface, TwoFactorInterface {

    use IdTrait;
    use UuidTrait;
    use SoftDeleteableEntity;

    #[ORM\Column(type: 'string', unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email(mode: 'html5')]
    #[Assert\Length(min: 4, max: 128)]
    private $username;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Assert\NotBlank(allowNull: true)]
    private ?string $firstname = null;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Assert\NotBlank(allowNull: true)]
    private ?string $lastname = null;

    #[ORM\Column(type: 'string', length: 62, nullable: true)]
    #[Serializer\Exclude]
    private $password;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Length(max: 191)]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $grade = null;

    #[ORM\ManyToOne(targetEntity: UserType::class, inversedBy: 'users')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    #[Assert\NotNull]
    #[Serializer\ReadOnlyProperty]
    #[Serializer\Accessor(getter: 'getTypeString')]
    #[Serializer\Type('string')]
    private ?UserType $type = null;

    #[ORM\Column(type: 'json')]
    #[Serializer\Exclude]
    private array $roles = [ 'ROLE_USER' ];

    #[ORM\Column(type: 'string', nullable: true)]
    private $externalId;

    #[ORM\Column(type: 'boolean')]
    private bool $isActive = true;

    #[ORM\Column(type: 'boolean')]
    #[Serializer\Exclude]
    private bool $isEmailConfirmationPending = false;

    #[ORM\JoinTable]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: ServiceProvider::class)]
    #[Serializer\Exclude]
    private $enabledServices;

    #[ORM\OneToMany(targetEntity: ServiceAttributeValue::class, mappedBy: 'user')]
    #[Serializer\Exclude]
    private $attributes;

    #[ORM\ManyToMany(targetEntity: UserRole::class, inversedBy: 'users')]
    #[Serializer\Exclude]
    private $userRoles;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Serializer\Exclude]
    private $googleAuthenticatorSecret;

    #[ORM\Column(type: 'json')]
    #[Serializer\Exclude]
    private array $backupCodes = [ ];

    #[ORM\Column(name: 'trusted_version', type: 'integer')]
    #[Serializer\Exclude]
    private int $trustedVersion = 0;

    #[ORM\Column(type: 'datetime')]
    #[Gedmo\Timestampable(on: 'create')]
    #[Serializer\Exclude]
    private $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Gedmo\Timestampable(on: 'update', field: ['firstname', 'lastname', 'email', 'type', 'userRoles'])]
    #[Serializer\Exclude]
    private $updatedAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $enabledFrom;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $enabledUntil;

    #[ORM\Column(type: 'json')]
    #[Serializer\Exclude]
    private array $data = [ ];

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTime $privacyPolicyConfirmedAt = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isProvisioned = true;

    #[ORM\Column(type: 'boolean')]
    private bool $mustChangePassword = false;

    #[ORM\Column(type: 'boolean', options: ['default' => 1])]
    private bool $canChangePassword = true;

    /**
     * @var Collection<User>
     */
    #[ORM\JoinTable(name: 'user_links')]
    #[ORM\JoinColumn(name: 'source_user_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'target_user_id', onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'parents')]
    private $linkedStudents;

    /**
     * @var Collection<User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'linkedStudents')]
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

    public function getUserIdentifier(): string {
        return $this->getUsername();
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
     * @return UserType|null
     */
    public function getType(): ?UserType {
        return $this->type;
    }

    /**
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
            return implode(',', $this->linkedStudents->map(fn(User $user) => $user->getEmail())->toArray());
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

    public function isEmailConfirmationPending(): bool {
        return $this->isEmailConfirmationPending;
    }

    public function setIsEmailConfirmationPending(bool $isEmailConfirmationPending): User {
        $this->isEmailConfirmationPending = $isEmailConfirmationPending;
        return $this;
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

    public function getUserRoles(): Collection {
        return $this->userRoles;
    }

    public function addUserRole(UserRole $role) {
        $this->userRoles->add($role);
    }

    public function removeUserRole(UserRole $role) {
        $this->userRoles->removeElement($role);
    }

    /**
     * @return string[]
     */
    public function getRoles(): array {
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

    public function getPassword(): string {
        return $this->password ?? '';
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

    public function isGoogleAuthenticatorEnabled(): bool {
        return $this->googleAuthenticatorSecret !== null;
    }

    public function getGoogleAuthenticatorUsername(): string {
        return $this->getUsername();
    }

    public function getTrustedTokenVersion(): int {
        return $this->trustedVersion;
    }

    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTime {
        return $this->updatedAt;
    }

    public function getEnabledFrom(): ?DateTime {
        return $this->enabledFrom;
    }

    public function setEnabledFrom(?DateTime $enabledFrom): User {
        $this->enabledFrom = $enabledFrom;
        return $this;
    }

    public function getEnabledUntil(): ?DateTime {
        return $this->enabledUntil;
    }

    public function setEnabledUntil(?DateTime$enabledUntil): User {
        $this->enabledUntil = $enabledUntil;
        return $this;
    }

    public function getData(string $key, $default = null) {
        return $this->data[$key] ?? $default;
    }

    public function setData(string $key, $value): void {
        $this->data[$key] = $value;
    }

    public function getPrivacyPolicyConfirmedAt(): ?DateTime {
        return $this->privacyPolicyConfirmedAt;
    }

    public function setPrivacyPolicyConfirmedAt(?DateTime $privacyPolicyConfirmedAt): User {
        $this->privacyPolicyConfirmedAt = $privacyPolicyConfirmedAt;
        return $this;
    }

    public function isProvisioned(): bool {
        return $this->isProvisioned;
    }

    public function setIsProvisioned(bool $isProvisioned): User {
        $this->isProvisioned = $isProvisioned;
        return $this;
    }

    public function isMustChangePassword(): bool {
        return $this->mustChangePassword;
    }

    public function setMustChangePassword(bool $mustChangePassword): User {
        $this->mustChangePassword = $mustChangePassword;
        return $this;
    }

    public function canChangePassword(): bool {
        return $this->canChangePassword;
    }

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

    public function getLinkedStudents(): Collection {
        return $this->linkedStudents;
    }

    public function getParents(): Collection {
        return $this->parents;
    }

    public function getTypeString(): string {
        return (string)$this->getType()->getUuid();
    }
}