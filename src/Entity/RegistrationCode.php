<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;
use Ramsey\Uuid\Uuid;
use Swagger\Annotations as SWG;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\GroupSequence;
use Symfony\Component\Validator\GroupSequenceProviderInterface;

/**
 * @ORM\Entity()
 * @UniqueEntity(fields={"code"})
 * @UniqueEntity(fields={"username"})
 * @UniqueEntity(fields={"token"})
 * @Assert\GroupSequenceProvider
 */
class RegistrationCode implements GroupSequenceProviderInterface {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\Column(type="string", unique=true, length=32)
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @var string
     */
    private $code;

    /**
     * @ORM\Column(type="datetime", nullable= true)
     * @Gedmo\Timestampable(on="change", field="redeemingUser")
     * @Serializer\Exclude()
     * @var \DateTime|null
     */
    private $redeemedAt = null;

    /**
     * The user which was created from this code.
     *
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Serializer\Exclude()
     * @var User|null
     */
    private $redeemingUser = null;

    /**
     * @ORM\Column(type="datetime", nullable= true)
     * @Serializer\Exclude()
     * @var \DateTime|null
     */
    private $confirmedAt;

    /**
     * @ORM\Column(type="string", unique=true, nullable=true)
     * @Assert\NotBlank(groups={"provide_username"})
     * @var string
     */
    private $username;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank(groups={"provide_suffix"})
     * @var string|null
     */
    private $usernameSuffix;

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
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Email()
     * @var string|null
     */
    private $email;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank(allowNull=true)
     * @var string|null
     */
    private $grade;

    /**
     * @ORM\ManyToOne(targetEntity="UserType")
     * @ORM\JoinColumn(onDelete="SET NULL")
     * @Serializer\ReadOnly()
     * @Serializer\Accessor(getter="getTypeString")
     * @Serializer\Type("string")
     * @SWG\Property(description="UUID of the usertype")
     */
    private $type;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank(allowNull=true)
     * @var string|null
     */
    private $externalId;

    /**
     * @ORM\OneToMany(targetEntity="ServiceAttributeRegistrationCodeValue", mappedBy="registrationCode")
     * @Serializer\Exclude()
     */
    private $attributes;

    /**
     * @ORM\Column(type="string", length=128, unique=true, nullable=true)
     * @Serializer\Exclude()
     * @var string|null
     */
    private $token = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="change", field="token")
     * @Serializer\Exclude()
     * @var \DateTime|null
     */
    private $tokenCreatedAt = null;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->attributes = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function getCode(): ?string {
        return $this->code;
    }

    /**
     * @param string|null $code
     * @return RegistrationCode
     */
    public function setCode(?string $code): RegistrationCode {
        $this->code = $code;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getRedeemedAt(): ?\DateTime {
        return $this->redeemedAt;
    }

    /**
     * @return User|null
     */
    public function getRedeemingUser(): ?User {
        return $this->redeemingUser;
    }

    /**
     * @param User|null $redeemingUser
     * @return RegistrationCode
     */
    public function setRedeemingUser(?User $redeemingUser): RegistrationCode {
        $this->redeemingUser = $redeemingUser;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getConfirmedAt(): ?\DateTime {
        return $this->confirmedAt;
    }

    /**
     * @param \DateTime|null $confirmedAt
     * @return RegistrationCode
     */
    public function setConfirmedAt(?\DateTime $confirmedAt): RegistrationCode {
        $this->confirmedAt = $confirmedAt;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUsername(): ?string {
        return $this->username;
    }

    /**
     * @param string|null $username
     * @return RegistrationCode
     */
    public function setUsername(?string $username): RegistrationCode {
        $this->username = $username;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUsernameSuffix(): ?string {
        return $this->usernameSuffix;
    }

    /**
     * @param string|null $usernameSuffix
     * @return RegistrationCode
     */
    public function setUsernameSuffix(?string $usernameSuffix): RegistrationCode {
        $this->usernameSuffix = $usernameSuffix;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFirstname(): ?string {
        return $this->firstname;
    }

    /**
     * @param string|null $firstname
     * @return RegistrationCode
     */
    public function setFirstname(?string $firstname): RegistrationCode {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastname(): ?string {
        return $this->lastname;
    }

    /**
     * @param string|null $lastname
     * @return RegistrationCode
     */
    public function setLastname(?string $lastname): RegistrationCode {
        $this->lastname = $lastname;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string {
        return $this->email;
    }

    /**
     * @param string|null $email
     * @return RegistrationCode
     */
    public function setEmail(?string $email): RegistrationCode {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getGrade(): ?string {
        return $this->grade;
    }

    /**
     * @param string|null $grade
     * @return RegistrationCode
     */
    public function setGrade(?string $grade): RegistrationCode {
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
     * @param UserType $type
     * @return RegistrationCode
     */
    public function setType(UserType $type) {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getExternalId(): ?string {
        return $this->externalId;
    }

    /**
     * @param string|null $externalId
     * @return RegistrationCode
     */
    public function setExternalId(?string $externalId): RegistrationCode {
        $this->externalId = $externalId;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getAttributes(): Collection {
        return $this->attributes;
    }

    /**
     * @param string[] $attributes
     * @return RegistrationCode
     */
    public function setAttributes(array $attributes): RegistrationCode {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * Returns whether this code has already been redeemed.
     *
     * @return bool
     */
    public function wasRedeemed(): bool {
        return $this->getRedeemedAt() !== null;
    }

    /**
     * @return string|null
     */
    public function getToken(): ?string {
        return $this->token;
    }

    /**
     * @param string|null $token
     * @return RegistrationCode
     */
    public function setToken(?string $token): RegistrationCode {
        $this->token = $token;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getTokenCreatedAt(): ?\DateTime {
        return $this->tokenCreatedAt;
    }

    public function getTypeString(): string {
        return (string)$this->getType()->getUuid();
    }

    /**
     * @inheritDoc
     */
    public function getGroupSequence() {
        if($this->usernameSuffix === null) {
            return [ 'Default', 'provide_username' ];
        }

        return ['Default', 'provide_suffix'];
    }
}