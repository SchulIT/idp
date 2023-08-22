<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class EmailConfirmation {

    use IdTrait;
    use UuidTrait;

    #[ORM\Column(type: 'string', length: 128, unique: true)]
    #[Assert\NotNull]
    private ?string $token = null;

    #[ORM\OneToOne(targetEntity: User::class, cascade: ['persist'])]
    #[ORM\JoinColumn(unique: true, onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\Column(type: 'text')]
    #[Assert\Email]
    #[Assert\NotNull]
    private ?string $emailAddress = null;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    public function getToken(): ?string {
        return $this->token;
    }

    public function setToken(?string $token): EmailConfirmation {
        $this->token = $token;
        return $this;
    }

    public function getUser(): ?User {
        return $this->user;
    }

    public function setUser(?User $user): EmailConfirmation {
        $this->user = $user;
        return $this;
    }

    public function getEmailAddress(): ?string {
        return $this->emailAddress;
    }

    public function setEmailAddress(?string $emailAddress): EmailConfirmation {
        $this->emailAddress = $emailAddress;
        return $this;
    }
}