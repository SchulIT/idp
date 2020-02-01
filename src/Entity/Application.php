<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity()
 * @UniqueEntity(fields={"name"})
 */
class Application implements UserInterface {

    /**
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=64, unique=true)
     * @Assert\Length(max="64")
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="application_scope")
     * @Assert\NotNull()
     * @var ApplicationScope
     */
    private $scope;

    /**
     * @ORM\ManyToOne(targetEntity="ServiceProvider")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var ServiceProvider|null
     */
    private $service = null;

    /**
     * @var string
     * @ORM\Column(type="string", length=64, unique=true)
     */
    private $apiKey;

    /**
     * @var string
     * @ORM\Column(name="`description`", type="text")
     * @Assert\NotBlank()
     */
    private $description;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastActivity;

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Application
     */
    public function setName($name): Application {
        $this->name = $name;
        return $this;
    }

    /**
     * @return ApplicationScope|null
     */
    public function getScope(): ?ApplicationScope {
        return $this->scope;
    }

    /**
     * @param ApplicationScope|null $scope
     * @return Application
     */
    public function setScope(?ApplicationScope $scope): Application {
        $this->scope = $scope;
        return $this;
    }

    /**
     * @return ServiceProvider|null
     */
    public function getService(): ?ServiceProvider {
        return $this->service;
    }

    /**
     * @param ServiceProvider|null $service
     * @return Application
     */
    public function setService(?ServiceProvider $service): Application {
        $this->service = $service;
        return $this;
    }

    /**
     * @return string
     */
    public function getApiKey() {
        return $this->apiKey;
    }

    /**
     * @param string $apiKey
     * @return Application
     */
    public function setApiKey($apiKey): Application {
        $this->apiKey = $apiKey;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Application
     */
    public function setDescription($description): Application {
        $this->description = $description;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getLastActivity(): ?\DateTime {
        return $this->lastActivity;
    }

    /**
     * @param \DateTime $lastActivity
     * @return Application
     */
    public function setLastActivity(\DateTime $lastActivity): Application {
        $this->lastActivity = $lastActivity;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRoles() {
        if($this->getScope()->equals(ApplicationScope::IdpExchange())) {
            return [ 'ROLE_IDPEXCHANGE' ];
        }

        return [
            'ROLE_API'
        ];
    }

    /**
     * @return string
     */
    public function getPassword() {
        return null;
    }

    /**
     * @return null|string
     */
    public function getSalt() {
        return null;
    }

    /**
     * @return string
     */
    public function getUsername() {
        return $this->getName();
    }

    /**
     * @return mixed
     */
    public function eraseCredentials() { }

    /**
     * @Assert\Callback()
     */
    public function validateService(ExecutionContextInterface $context, $payload) {
        if($this->getScope()->equals(ApplicationScope::IdpExchange())) {
            if($this->getService() === null) {
                $context->buildViolation('This value should not be blank.')
                    ->atPath('service')
                    ->addViolation();
            }
        }
    }
}