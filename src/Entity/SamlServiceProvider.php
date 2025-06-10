<?php

namespace App\Entity;

use App\Form\DataTransformer\KeyValueContainer;
use App\Utils\ArrayUtils;
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

    #[ORM\Column(type: 'json', nullable: true)]
    #[Assert\All(constraints: [
        new Assert\NotBlank(),
        new Assert\Url()
    ])]
    #[Serializer\Exclude]
    private ?array $acsUrls = [];

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

    /**
     * @var array<string, string>
     */
    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $attributeNameMapping = [ ];

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

    /**
     * @return string[]|null
     */
    public function getAcsUrls(): ?array {
        return $this->acsUrls;
    }

    /**
     * @param string[]|null $acsUrls
     * @return $this
     */
    public function setAcsUrls(?array $acsUrls): self {
        $this->acsUrls = $acsUrls;
        return $this;
    }

    public function getCertificate(): string {
        return $this->certificate;
    }

    public function setCertificate(string $certificate): self {
        $this->certificate = $certificate;
        return $this;
    }

    public function getAttributeNameMapping(): array {
        return $this->attributeNameMapping ?? [ ];
    }

    public function setAttributeNameMapping(KeyValueContainer|array $attributeNameMapping): SamlServiceProvider {
        $this->attributeNameMapping = ArrayUtils::iterableKeyValueContainerToArray($attributeNameMapping);

        if(count($this->attributeNameMapping) === 0) {
            $this->attributeNameMapping = null;
        }

        return $this;
    }

    /**
     * @return Collection<ServiceAttribute>
     */
    public function getAttributes(): Collection {
        return $this->attributes;
    }
}