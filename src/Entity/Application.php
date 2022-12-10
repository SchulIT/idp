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
    private string $name;

    #[ORM\Column(type: 'string', enumType: ApplicationScope::class)]
    #[Assert\NotNull]
    private ?ApplicationScope $scope = null;

    #[ORM\ManyToOne(targetEntity: 'SamlServiceProvider')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?SamlServiceProvider $service = null;

    #[ORM\Column(type: 'string', length: 64, unique: true)]
    private string $apiKey;

    #[ORM\Column(name: '`description`', type: 'text')]
    #[Assert\NotBlank]
    private string $description;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $lastActivity;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name): Application {
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
     * @return string
     */
    public function getApiKey() {
        return $this->apiKey;
    }

    /**
     * @param string $apiKey
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
     */
    public function setDescription($description): Application {
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

    /**
     * @return mixed
     */
    public function getRoles(): array {
        if($this->getScope()->equals === ApplicationScope::IdpExchange) {
            return [ 'ROLE_IDPEXCHANGE' ];
        }

        if($this->scope === ApplicationScope::AdConnect) {
            return [ 'ROLE_ADCONNECT' ];
        }

        return [
            'ROLE_API'
        ];
    }

    /**
     * @return string
     */
    public function getPassword() {
        return '';
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

    public function getUserIdentifier(): string {
        return $this->getName();
    }

    /**
     * @return mixed
     */
    public function eraseCredentials() { }

    #[Assert\Callback]
    public function validateService(ExecutionContextInterface $context, $payload) {
        if($this->getScope() === ApplicationScope::IdpExchange) {
            if($this->getService() === null) {
                $context->buildViolation('This value should not be blank.')
                    ->atPath('service')
                    ->addViolation();
            }
        }
    }
}