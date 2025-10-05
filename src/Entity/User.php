<?php

declare(strict_types=1);

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
#[UniqueEntity(fields: ['email'])]
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
    private string $username;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Assert\NotBlank(allowNull: true)]
    private ?string $firstname = null;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Assert\NotBlank(allowNull: true)]
    private ?string $lastname = null;

    #[ORM\Column(type: 'string', length: 62, nullable: true)]
    #[Serializer\Exclude]
    private ?string $password = null;

    #[ORM\Column(type: 'string', unique: true, nullable: true)]
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
    private ?string $externalId = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isActive = true;

    /**
     * @var Collection<ServiceProvider>
     */
    #[ORM\ManyToMany(targetEntity: ServiceProvider::class)]
    #[ORM\JoinTable]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(onDelete: 'CASCADE')]
    #[Serializer\Exclude]
    private Collection $enabledServices;

    /**
     * @var Collection<ServiceAttributeValue>
     */
    #[ORM\OneToMany(targetEntity: ServiceAttributeValue::class, mappedBy: 'user')]
    #[Serializer\Exclude]
    private Collection $attributes;

    /**
     * @var Collection<UserRole>
     */
    #[ORM\ManyToMany(targetEntity: UserRole::class, inversedBy: 'users')]
    #[ORM\JoinTable]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(onDelete: 'CASCADE')]
    #[Serializer\Exclude]
    private Collection $userRoles;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Serializer\Exclude]
    private ?string $googleAuthenticatorSecret = null;

    #[ORM\Column(type: 'json')]
    #[Serializer\Exclude]
    private array $backupCodes = [ ];

    #[ORM\Column(name: 'trusted_version', type: 'integer')]
    #[Serializer\Exclude]
    private int $trustedVersion = 0;

    #[ORM\Column(type: 'datetime')]
    #[Gedmo\Timestampable(on: 'create')]
    #[Serializer\Exclude]
    private DateTime $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Gedmo\Timestampable(on: 'update', field: ['firstname', 'lastname', 'email', 'type', 'userRoles'])]
    #[Serializer\Exclude]
    private ?DateTime $updatedAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTime $enabledFrom = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTime $enabledUntil = null;

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
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'parents')]
    #[ORM\JoinTable(name: 'user_links')]
    #[ORM\JoinColumn(name: 'source_user_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'target_user_id', onDelete: 'CASCADE')]
    private Collection $linkedStudents;

    /**
     * @var Collection<User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'linkedStudents')]
    private Collection $parents;

    #[ORM\OneToOne(targetEntity: EmailConfirmation::class, mappedBy: 'user')]
    private ?EmailConfirmation $emailConfirmation = null;

    public function __construct() {
        $this->uuid = Uuid::uuid4();

        $this->enabledServices = new ArrayCollection();
        $this->attributes = new ArrayCollection();
        $this->userRoles = new ArrayCollection();
        $this->linkedStudents = new ArrayCollection();
        $this->parents = new ArrayCollection();
    }

    public function getUsername(): string {
        return $this->username;
    }

    public function getUserIdentifier(): string {
        return $this->getUsername();
    }

    public function setUsername(string $username): self {
        $this->username = mb_strtolower($username);
        return $this;
    }

    public function getFirstname(): ?string {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self {
        $this->firstname = $firstname;
        return $this;
    }

    public function getLastname(): ?string {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self {
        $this->lastname = $lastname;
        return $this;
    }

    public function setPassword(string $password): self {
        $this->password = $password;
        return $this;
    }


    public function setEmail(?string $email): self {
        $this->email = $email;
        return $this;
    }

    public function getEmail(): ?string {
        return $this->email;
    }


    public function getGrade(): ?string {
        return $this->grade;
    }

    public function setGrade(?string $grade): self {
        $this->grade = $grade;
        return $this;
    }

    public function getType(): ?UserType {
        return $this->type;
    }

    public function setType(UserType $userType): self {
        $this->type = $userType;
        return $this;
    }

    public function getExternalId(): ?string {
        if($this->linkedStudents->count() > 0) {
            return implode(',', $this->linkedStudents->map(fn(User $user): ?string => $user->getEmail())->toArray());
        }

        return $this->externalId;
    }

    public function setExternalId(?string $externalId): self {
        $this->externalId = $externalId;
        return $this;
    }

    public function isActive(): bool {
        return $this->isActive;
    }

    public function setIsActive(bool $active): self {
        $this->isActive = $active;
        return $this;
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

    public function getUserRoles(): Collection {
        return $this->userRoles;
    }

    public function addUserRole(UserRole $role): void {
        $this->userRoles->add($role);
    }

    public function removeUserRole(UserRole $role): void {
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
     */
    public function setRoles(array $roles): self {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): string {
        return $this->password ?? '';
    }

    public function getSalt(): null {
        return null;
    }

    public function eraseCredentials(): void
    {
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

    public function setGoogleAuthenticatorSecret(?string $googleAuthenticatorSecret): void {
        $this->googleAuthenticatorSecret = $googleAuthenticatorSecret;
    }

    /**
     * @return string[]
     */
    public function getBackupCodes(): array {
        return $this->backupCodes;
    }

    public function emptyBackupCodes(): void {
        $this->backupCodes = [ ];
    }

    /**
     * @param string[] $backupCodes
     */
    public function setBackupCodes(array $backupCodes): self {
        $this->backupCodes = $backupCodes;
        return $this;
    }

    public function isBackupCode(string $code): bool {
        return in_array($code, $this->backupCodes);
    }

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

    public function getEmailConfirmation(): ?EmailConfirmation {
        return $this->emailConfirmation;
    }

    public function getTypeString(): string {
        return (string)$this->getType()->getUuid();
    }
}
