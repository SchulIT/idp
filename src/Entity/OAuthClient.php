<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use League\Bundle\OAuth2ServerBundle\Model\AbstractClient;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'oauth2_client')]
#[UniqueEntity(fields: ['identifier'])]
#[UniqueEntity(fields: ['scopeName'])]
class OAuthClient extends AbstractClient {

    /** @var non-empty-string */
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36, unique: true)]
    #[Assert\NotBlank]
    protected string $identifier;

    #[ORM\Column(type: 'string', length: 32, unique: true)]
    #[Assert\NotBlank]
    private string $scopeName;

    #[ORM\OneToOne(targetEntity: OidcServiceProvider::class, mappedBy: 'client')]
    #[Assert\NotNull]
    private OidcServiceProvider|null $serviceProvider = null;

    public function getIdentifier(): string {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): OAuthClient {
        $this->identifier = $identifier;
        return $this;
    }

    public function getScopeName(): string {
        return $this->scopeName;
    }

    public function setScopeName(string $scopeName): OAuthClient {
        $this->scopeName = $scopeName;
        return $this;
    }

    public function getServiceProvider(): OidcServiceProvider {
        return $this->serviceProvider;
    }

    public function setServiceProvider(OidcServiceProvider $serviceProvider): OAuthClient {
        $this->serviceProvider = $serviceProvider;
        return $this;
    }
}