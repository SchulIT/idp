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

/**
 * @Serializer\Discriminator(disabled=true)
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=true)
 */
#[ORM\Entity]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'class', type: 'string')]
#[ORM\DiscriminatorMap(['user' => 'User', 'ad' => 'ActiveDirectoryUser'])]
#[UniqueEntity(fields: ['username'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface, TwoFactorInterface {

    use IdTrait;
    use UuidTrait;
    use SoftDeleteableEntity;

    #[ORM\Column(type: 'string', unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email(mode: 'html5')]
    #[Assert\Length(max: 128, min: 4)]
    private $username;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Assert\NotBlank(allowNull: true)]
    private ?string $firstname = null;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Assert\NotBlank(allowNull: true)]
    private ?string $lastname = null;

    /**
     * @Serializer\Exclude()
     */
    #[ORM\Column(type: 'string', length: 62, nullable: true)]
    private $password;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Length(max: 191)]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $grade = null;

    /**
     * @Serializer\ReadOnlyProperty()
     * @Serializer\Accessor(getter="getTypeString")
     * @Serializer\Type("string")
     */
    #[ORM\ManyToOne(targetEntity: 'UserType', inversedBy: 'users')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    #[Assert\NotNull]
    private ?UserType $type = null;

    /**
     * @Serializer\Exclude()
     */
    #[ORM\Column(type: 'json')]
    private array $roles = [ 'ROLE_USER' ];

    #[ORM\Column(type: 'string', nullable: true)]
    private $externalId;

    #[ORM\Column(type: 'boolean')]
    private bool $isActive = true;

    /**
     * @Serializer\Exclude()
     */
    #[ORM\Column(type: 'boolean')]
    private bool $isEmailConfirmationPending = false;

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
    #[ORM\OneToMany(targetEntity: 'ServiceAttributeValue', mappedBy: 'user')]
    private $attributes;

    /**
     * @Serializer\Exclude()
     */
    #[ORM\ManyToMany(targetEntity: 'UserRole', inversedBy: 'users')]
    private $userRoles;

    /**
     * @Serializer\Exclude()
     */
    #[ORM\Column(type: 'string', nullable: true)]
    private $googleAuthenticatorSecret;

    /**
     * @Serializer\Exclude()
     */
    #[ORM\Column(type: 'json')]
    private array $backupCodes = [ ];

    /**
     * @Serializer\Exclude()
     */
    #[ORM\Column(type: 'integer', name: 'trusted_version')]
    private int $trustedVersion = 0;

    /**
     * @Gedmo\Timestampable(on="create")
     * @Serializer\Exclude()
     */
    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    /**
     * @Gedmo\Timestampable(on="update", field={"firstname", "lastname", "email", "type", "userRoles"})
     * @Serializer\Exclude()
     */
    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updatedAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $enabledFrom;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $enabledUntil;

    /**
     * @Serializer\Exclude()
     */
    #[ORM\Column(type: 'json')]
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
    #[ORM\ManyToMany(targetEntity: 'User', inversedBy: 'parents')]
    private $linkedStudents;

    /**
     * @var Collection<User>
     */
    #[ORM\ManyToMany(targetEntity: 'User', mappedBy: 'linkedStudents')]
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
     * @return UserType
     */
    public function getType() {
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
            return implode(',', $this->linkedStudents->map(fn(User $user) => $user->getExternalId())->toArray());
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