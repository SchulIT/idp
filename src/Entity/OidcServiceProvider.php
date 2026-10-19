<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class OidcServiceProvider extends ServiceProvider {

    #[ORM\OneToOne(targetEntity: OAuthClient::class, inversedBy: 'serviceProvider', cascade: ['all'])]
    #[ORM\JoinColumn(referencedColumnName: 'identifier', nullable: true, onDelete: 'CASCADE')]
    #[Assert\Valid]
    #[Assert\NotNull]
    private OAuthClient|null $client = null;

    public function getClient(): OAuthClient|null {
        return $this->client;
    }

    public function setClient(OAuthClient $client): OidcServiceProvider {
        $client->setServiceProvider($this);

        $this->client = $client;
        return $this;
    }

    public function __construct() {
        parent::__construct();
    }
}