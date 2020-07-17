<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class EmailConfirmation {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\Column(type="string", unique=true, length=128)
     * @Assert\NotNull()
     * @var string|null
     */
    private $token;

    /**
     * @ORM\OneToOne(targetEntity="User", cascade={"persist"})
     * @ORM\JoinColumn(unique=true, onDelete="CASCADE")
     * @var User|null
     */
    private $user;

    /**
     * @ORM\Column(type="text")
     * @Assert\Email()
     * @Assert\NotNull()
     * @var string|null
     */
    private $emailAddress;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotNull()
     * @var DateTime|null
     */
    private $validUntil;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    /**
     * @return string|null
     */
    public function getToken(): ?string {
        return $this->token;
    }

    /**
     * @param string|null $token
     * @return EmailConfirmation
     */
    public function setToken(?string $token): EmailConfirmation {
        $this->token = $token;
        return $this;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User {
        return $this->user;
    }

    /**
     * @param User|null $user
     * @return EmailConfirmation
     */
    public function setUser(?User $user): EmailConfirmation {
        $this->user = $user;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmailAddress(): ?string {
        return $this->emailAddress;
    }

    /**
     * @param string|null $emailAddress
     * @return EmailConfirmation
     */
    public function setEmailAddress(?string $emailAddress): EmailConfirmation {
        $this->emailAddress = $emailAddress;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getValidUntil(): ?DateTime {
        return $this->validUntil;
    }

    /**
     * @param DateTime|null $validUntil
     * @return EmailConfirmation
     */
    public function setValidUntil(?DateTime $validUntil): EmailConfirmation {
        $this->validUntil = $validUntil;
        return $this;
    }
}