<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;
use R\U2FTwoFactorBundle\Model\U2F\TwoFactorInterface as U2FTwoFactorInterface;
use R\U2FTwoFactorBundle\Model\U2F\U2FKey;
use Scheb\TwoFactorBundle\Model\BackupCodeInterface;
use Scheb\TwoFactorBundle\Model\PreferredProviderInterface;
use Scheb\TwoFactorBundle\Model\TrustedDeviceInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Scheb\TwoFactorBundle\Model\Google\TwoFactorInterface as GoogleTwoFactorInterface;

/**
 * @ORM\Entity()
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="class", type="string")
 * @ORM\DiscriminatorMap({"user" = "User", "ad" = "ActiveDirectoryUser"})
 * @ORM\Table(options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"})
 * @UniqueEntity(fields={"email", "username"})
 */
class User implements UserInterface, GoogleTwoFactorInterface, TrustedDeviceInterface, BackupCodeInterface, U2FTwoFactorInterface, PreferredProviderInterface {

    /**
     * @ORM\GeneratedValue()
     * @ORM\Id()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64, unique=true)
     * @ORM\OrderBy()
     * @Assert\Length(max="64", min="4")
     */
    private $username;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $firstname;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=62)
     * @Serializer\Exclude()
     */
    private $password;

    /**
     * @ORM\Column(type="string", unique=true, length=191)
     * @Assert\Email()
     * @Assert\NotBlank()
     * @Assert\Length(max="191")
     */
    private $email;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $grade;

    /**
     * @ORM\ManyToOne(targetEntity="UserType", inversedBy="users")
     * @ORM\JoinColumn()
     */
    private $type;

    /**
     * @ORM\Column(type="json_array")
     * @Serializer\Exclude()
     */
    private $roles = [ 'ROLE_USER' ];

    /**
     * @ORM\Column(type="string", name="internal_id", nullable=true)
     */
    private $internalId;

    /**
     * @ORM\Column(type="boolean", name="is_active")
     */
    private $isActive = true;

    /**
     * @ORM\ManyToMany(targetEntity="ServiceProvider")
     * @ORM\JoinTable(
     *  joinColumns={@ORM\JoinColumn(name="user", referencedColumnName="id", onDelete="CASCADE")},
     *  inverseJoinColumns={@ORM\JoinColumn(name="service", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    private $enabledServices;

    /**
     * @ORM\OneToMany(targetEntity="ServiceAttributeValue", mappedBy="user")
     */
    private $attributes;

    /**
     * @ORM\ManyToMany(targetEntity="UserRole", inversedBy="users")
     */
    private $userRoles;

    /**
     * @ORM\Column(type="string", name="google_authenticator_secret", nullable=true)
     * @Serializer\Exclude()
     */
    private $googleAuthenticatorSecret;

    /**
     * @ORM\Column(type="json_array")
     * @Serializer\Exclude()
     */
    private $backupCodes = [ ];

    /**
     * @ORM\Column(type="integer", name="trusted_version")
     * @Serializer\Exclude()
     */
    private $trustedVersion;

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
     * @ORM\OneToMany(targetEntity="App\Entity\U2fKey", mappedBy="user")
     */
    private $u2fKeys;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $enabledFrom;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $enabledUntil;

    public function __construct() {
        $this->enabledServices = new ArrayCollection();
        $this->attributes = new ArrayCollection();
        $this->userRoles = new ArrayCollection();
        $this->u2fKeys = new ArrayCollection();
    }

    public function getId() {
        return $this->id;
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
        $this->username = $username;
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstname() {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     * @return User
     */
    public function setFirstname($firstname) {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastname() {
        return $this->lastname;
    }

    /**
     * @param string $lastname
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
     * @param string $email
     * @return User
     */
    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
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
     * @return string|int
     */
    public function getInternalId() {
        return $this->internalId;
    }

    /**
     * @param string|int $internalId
     * @return User
     */
    public function setInternalId($internalId) {
        $this->internalId = $internalId;
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
     * @return ArrayCollection
     */
    public function getUserRoles() {
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
     * @inheritDoc
     */
    public function isU2FAuthEnabled() {
        return count($this->u2fKeys) > 0;
    }

    /**
     * @inheritDoc
     */
    public function getU2FKeys() {
        return $this->u2fKeys;
    }

    /**
     * @inheritDoc
     */
    public function addU2FKey($key) {
        $this->u2fKeys->add($key);
    }

    /**
     * @inheritDoc
     */
    public function removeU2FKey($key) {
        $this->u2fKeys->removeElement($key);
    }

    /**
     * @inheritDoc
     */
    public function getPreferredTwoFactorProvider(): ?string {
        if($this->isU2FAuthEnabled()) {
            return 'u2f_two_factor';
        } else if($this->isGoogleAuthenticatorEnabled()) {
            return 'google';
        }

        return null;
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
}