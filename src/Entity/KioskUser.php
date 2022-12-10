<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[UniqueEntity(fields: ['token'])]
class KioskUser {

    use IdTrait;

    use UuidTrait;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[Assert\NotNull]
    private ?User $user = null;

    #[ORM\Column(type: 'string', unique: true)]
    private ?string $token = null;

    #[ORM\Column(type: 'text')]
    private ?string $ipAddresses = null;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    public function getUser(): ?User {
        return $this->user;
    }

    public function setUser(?User $user): KioskUser {
        $this->user = $user;
        return $this;
    }

    public function getToken(): string {
        return $this->token;
    }

    public function setToken(string $token): KioskUser {
        $this->token = $token;
        return $this;
    }

    public function getIpAddresses(): ?string {
        return $this->ipAddresses;
    }

    public function setIpAddresses(?string $ipAddresses): KioskUser {
        $this->ipAddresses = $ipAddresses;
        return $this;
    }
}