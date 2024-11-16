<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'class', type: 'string')]
#[ORM\DiscriminatorMap(['saml' => 'SamlServiceProvider', 'normal' => 'ServiceProvider'])]
class ServiceProvider {

    use IdTrait;
    use UuidTrait;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    private string $name;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    private string $description;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    #[Assert\Url]
    private string $url;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\NotBlank(allowNull: true)]
    private ?string $icon = null;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): self {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function setDescription(string $description): self {
        $this->description = $description;
        return $this;
    }

    public function getUrl(): string {
        return $this->url;
    }

    public function setUrl(string $url): self {
        $this->url = $url;
        return $this;
    }

    public function getIcon(): ?string {
        return $this->icon;
    }

    public function setIcon(?string $icon): ServiceProvider {
        $this->icon = $icon;
        return $this;
    }

}