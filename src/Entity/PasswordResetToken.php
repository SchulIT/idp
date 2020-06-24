<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity()
 */
class PasswordResetToken {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\Column(type="string", length=64, unique=true)
     */
    private $token;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private $expiresAt;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    /**
     * @return string
     */
    public function getToken(): string {
        return $this->token;
    }

    /**
     * @param string $token
     * @return PasswordResetToken
     */
    public function setToken(string $token): PasswordResetToken {
        $this->token = $token;
        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User {
        return $this->user;
    }

    /**
     * @param User $user
     * @return PasswordResetToken
     */
    public function setUser(User $user): PasswordResetToken {
        $this->user = $user;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getExpiresAt(): \DateTime {
        return $this->expiresAt;
    }

    /**
     * @param \DateTime $dateTime
     * @return PasswordResetToken
     */
    public function setExpiresAt(\DateTime $dateTime): PasswordResetToken {
        $this->expiresAt = $dateTime;
        return $this;
    }

}