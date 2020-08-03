<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @UniqueEntity(fields={"token"})
 */
class KioskUser {

    use IdTrait;

    use UuidTrait;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Assert\NotNull()
     * @var User|null
     */
    private $user;

    /**
     * @ORM\Column(type="string", unique=true)
     * @var string
     */
    private $token;

    /**
     * @ORM\Column(type="text")
     * @var string|null
     */
    private $ipAddresses;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User {
        return $this->user;
    }

    /**
     * @param User|null $user
     * @return KioskUser
     */
    public function setUser(?User $user): KioskUser {
        $this->user = $user;
        return $this;
    }

    /**
     * @return string
     */
    public function getToken(): string {
        return $this->token;
    }

    /**
     * @param string $token
     * @return KioskUser
     */
    public function setToken(string $token): KioskUser {
        $this->token = $token;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getIpAddresses(): ?string {
        return $this->ipAddresses;
    }

    /**
     * @param string|null $ipAddresses
     * @return KioskUser
     */
    public function setIpAddresses(?string $ipAddresses): KioskUser {
        $this->ipAddresses = $ipAddresses;
        return $this;
    }
}