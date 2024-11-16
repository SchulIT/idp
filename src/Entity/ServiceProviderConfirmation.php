<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity]
class ServiceProviderConfirmation {

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?User $user;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: ServiceProvider::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?ServiceProvider $serviceProvider;

    #[ORM\Column(type: 'datetime')]
    #[Gedmo\Timestampable(on: 'update')]
    private DateTime $dateTime;

    #[ORM\Column(type: 'json')]
    private array $attributes = [ ];

    public function __construct() {
        $this->dateTime = new DateTime();
    }

    public function getUser(): ?User {
        return $this->user;
    }

    public function setUser(User $user): self {
        $this->user = $user;
        return $this;
    }

    public function getServiceProvider(): ?ServiceProvider {
        return $this->serviceProvider;
    }

    public function setServiceProvider(ServiceProvider $serviceProvider): self {
        $this->serviceProvider = $serviceProvider;
        return $this;
    }

    public function getDateTime(): ?DateTime {
        return $this->dateTime;
    }

    /**
     * @return string[]
     */
    public function getAttributes(): array {
        return $this->attributes;
    }

    /**
     * @param string[] $attributes
     * @return ServiceProviderConfirmation
     */
    public function setAttributes(array $attributes): self {
        $this->attributes = $attributes;
        return $this;
    }
}