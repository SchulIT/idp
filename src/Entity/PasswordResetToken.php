<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class PasswordResetToken {

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64, unique=true)
     */
    private $token;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn()
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private $expiresAt;

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
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
     * @param mixed $user
     * @return PasswordResetToken
     */
    public function setUser(User $user): PasswordResetToken {
        $this->user = $user;
        return $this;
    }

    /**
     * @return mixed
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