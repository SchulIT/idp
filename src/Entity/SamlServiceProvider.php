<?php

namespace App\Entity;

use App\Validator\X509Certificate;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class SamlServiceProvider extends ServiceProvider {
    #[ORM\Column(type: 'string', unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 128)]
    private string $entityId;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    #[Assert\Url]
    #[Serializer\Exclude]
    private string $acs;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    #[Serializer\Exclude]
    #[X509Certificate]
    private string $certificate;

    /**
     * @var Collection<ServiceAttribute>
     */
    #[ORM\ManyToMany(targetEntity: ServiceAttribute::class, mappedBy: 'services')]
    #[Serializer\Exclude]
    private Collection $attributes;

    public function __construct() {
        parent::__construct();
        $this->attributes = new ArrayCollection();
    }

    public function getEntityId(): string {
        return $this->entityId;
    }

    public function setEntityId(string $entityId): self {
        $this->entityId = $entityId;
        return $this;
    }

    public function getAcs(): string {
        return $this->acs;
    }

    public function setAcs(string $acs): self {
        $this->acs = $acs;
        return $this;
    }

    public function getCertificate(): string {
        return $this->certificate;
    }

    public function setCertificate(string $certificate): self {
        $this->certificate = $certificate;
        return $this;
    }

    /**
     * @return Collection<ServiceAttribute>
     */
    public function getAttributes(): Collection {
        return $this->attributes;
    }
}