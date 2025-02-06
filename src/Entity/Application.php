<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity]
#[UniqueEntity(fields: ['name'])]
class Application implements UserInterface {

    use IdTrait;
    use UuidTrait;

    #[ORM\Column(type: 'string', length: 64, unique: true)]
    #[Assert\Length(max: 64)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(type: 'string', enumType: ApplicationScope::class)]
    #[Assert\NotNull]
    private ?ApplicationScope $scope = null;

    #[ORM\ManyToOne(targetEntity: SamlServiceProvider::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?SamlServiceProvider $service = null;

    #[ORM\Column(type: 'string', length: 64, unique: true)]
    #[Assert\NotBlank]
    private ?string $apiKey = null;

    #[ORM\Column(name: 'description', type: 'text')]
    #[Assert\NotBlank]
    private ?string $description = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTime $lastActivity = null;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(?string $name): Application {
        $this->name = $name;
        return $this;
    }

    public function getScope(): ?ApplicationScope {
        return $this->scope;
    }

    public function setScope(?ApplicationScope $scope): Application {
        $this->scope = $scope;
        return $this;
    }

    public function getService(): ?SamlServiceProvider {
        return $this->service;
    }

    public function setService(?SamlServiceProvider $service): Application {
        $this->service = $service;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getApiKey(): ?string {
        return $this->apiKey;
    }

    /**
     * @param string|null $apiKey
     * @return Application
     */
    public function setApiKey(?string $apiKey): Application {
        $this->apiKey = $apiKey;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return Application
     */
    public function setDescription(?string $description): Application {
        $this->description = $description;
        return $this;
    }

    public function getLastActivity(): ?DateTime {
        return $this->lastActivity;
    }

    public function setLastActivity(DateTime $lastActivity): Application {
        $this->lastActivity = $lastActivity;
        return $this;
    }

    public function getRoles(): array {
        if($this->scope === ApplicationScope::AdConnect) {
            return [ 'ROLE_ADCONNECT' ];
        }

        return [
            'ROLE_API'
        ];
    }

    public function getUsername(): ?string {
        return $this->getName();
    }

    public function getUserIdentifier(): string {
        return $this->getName();
    }

    public function eraseCredentials(): void { }

}