<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

#[ORM\Entity]
class PasswordResetToken {

    use IdTrait;
    use UuidTrait;

    #[ORM\Column(type: 'string', length: 64, unique: true)]
    private $token;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private $user;

    #[ORM\Column(type: 'datetime')]
    private $expiresAt;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    public function getToken(): string {
        return $this->token;
    }

    public function setToken(string $token): PasswordResetToken {
        $this->token = $token;
        return $this;
    }

    public function getUser(): User {
        return $this->user;
    }

    public function setUser(User $user): PasswordResetToken {
        $this->user = $user;
        return $this;
    }

    public function getExpiresAt(): DateTime {
        return $this->expiresAt;
    }

    public function setExpiresAt(DateTime $dateTime): PasswordResetToken {
        $this->expiresAt = $dateTime;
        return $this;
    }

}