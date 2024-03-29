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
    private $entityId;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    #[Assert\Url]
    #[Serializer\Exclude]
    private $acs;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    #[Serializer\Exclude]
    #[X509Certificate]
    private $certificate;

    #[ORM\ManyToMany(targetEntity: ServiceAttribute::class, mappedBy: 'services')]
    #[Serializer\Exclude]
    private $attributes;

    public function __construct() {
        parent::__construct();
        $this->attributes = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getEntityId() {
        return $this->entityId;
    }

    /**
     * @param string $entityId
     * @return ServiceProvider
     */
    public function setEntityId($entityId) {
        $this->entityId = $entityId;
        return $this;
    }

    /**
     * @return string
     */
    public function getAcs() {
        return $this->acs;
    }

    /**
     * @param string $acs
     * @return ServiceProvider
     */
    public function setAcs($acs) {
        $this->acs = $acs;
        return $this;
    }

    /**
     * @return string
     */
    public function getCertificate() {
        return $this->certificate;
    }

    /**
     * @param string $certificate
     * @return ServiceProvider
     */
    public function setCertificate($certificate) {
        $this->certificate = $certificate;
        return $this;
    }

    public function getAttributes(): Collection {
        return $this->attributes;
    }
}