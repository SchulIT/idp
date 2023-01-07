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
    private $user;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: ServiceProvider::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private $serviceProvider;

    #[ORM\Column(type: 'datetime')]
    #[Gedmo\Timestampable(on: 'update')]
    private $dateTime;

    #[ORM\Column(type: 'json')]
    private array $attributes = [ ];

    public function __construct() {
        $this->dateTime = new DateTime();
    }

    /**
     * @return User|null
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * @return ServiceProviderConfirmation
     */
    public function setUser(User $user) {
        $this->user = $user;
        return $this;
    }

    /**
     * @return ServiceProvider|null
     */
    public function getServiceProvider() {
        return $this->serviceProvider;
    }

    /**
     * @return ServiceProviderConfirmation
     */
    public function setServiceProvider(ServiceProvider $serviceProvider) {
        $this->serviceProvider = $serviceProvider;
        return $this;
    }

    public function getDateTime(): ?DateTime {
        return $this->dateTime;
    }

    /**
     * @return string[]
     */
    public function getAttributes() {
        return $this->attributes;
    }

    /**
     * @param string[] $attributes
     * @return ServiceProviderConfirmation
     */
    public function setAttributes(array $attributes) {
        $this->attributes = $attributes;
        return $this;
    }
}