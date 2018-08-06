<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

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
}