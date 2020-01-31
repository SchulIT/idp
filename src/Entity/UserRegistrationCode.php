<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @UniqueEntity(fields={"code"})
 * @UniqueEntity(fields={"username"})
 * @UniqueEntity(fields={"token"})
 */
class UserRegistrationCode {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

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
     * @var \DateTime|null
     */
    private $redeemedAt = null;

    /**
     * The user which was created from this code.
     *
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var User|null
     */
    private $redeemingUser = null;

    /**
     * @ORM\Column(type="datetime", nullable= true)
     * @var \DateTime|null
     */
    private $confirmedAt;

    /**
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotBlank()
     * @var string
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
     * @ORM\JoinColumn()
     */
    private $type;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank(allowNull=true)
     * @var string|null
     */
    private $internalId;

    /**
     * @ORM\Column(type="json_array")
     * @var string[]
     */
    private $attributes = [ ];

    /**
     * @ORM\Column(type="string", length=128, unique=true, nullable=true)
     * @var string|null
     */
    private $token = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="change", field="token")
     * @var \DateTime|null
     */
    private $tokenCreatedAt = null;

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getCode(): ?string {
        return $this->code;
    }

    /**
     * @param string|null $code
     * @return UserRegistrationCode
     */
    public function setCode(?string $code): UserRegistrationCode {
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
     * @return UserRegistrationCode
     */
    public function setRedeemingUser(?User $redeemingUser): UserRegistrationCode {
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
     * @return UserRegistrationCode
     */
    public function setConfirmedAt(?\DateTime $confirmedAt): UserRegistrationCode {
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
     * @return UserRegistrationCode
     */
    public function setUsername(?string $username): UserRegistrationCode {
        $this->username = $username;
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
     * @return UserRegistrationCode
     */
    public function setFirstname(?string $firstname): UserRegistrationCode {
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
     * @return UserRegistrationCode
     */
    public function setLastname(?string $lastname): UserRegistrationCode {
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
     * @return UserRegistrationCode
     */
    public function setEmail(?string $email): UserRegistrationCode {
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
     * @return UserRegistrationCode
     */
    public function setGrade(?string $grade): UserRegistrationCode {
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
     * @return UserRegistrationCode
     */
    public function setType(UserType $type) {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getInternalId(): ?string {
        return $this->internalId;
    }

    /**
     * @param string|null $internalId
     * @return UserRegistrationCode
     */
    public function setInternalId(?string $internalId): UserRegistrationCode {
        $this->internalId = $internalId;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getAttributes(): array {
        return $this->attributes;
    }

    /**
     * @param string[] $attributes
     * @return UserRegistrationCode
     */
    public function setAttributes(array $attributes): UserRegistrationCode {
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
     * @return UserRegistrationCode
     */
    public function setToken(?string $token): UserRegistrationCode {
        $this->token = $token;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getTokenCreatedAt(): ?\DateTime {
        return $this->tokenCreatedAt;
    }

}