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
    private $name;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    private $description;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    #[Assert\Url]
    private $url;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\NotBlank(allowNull: true)]
    private ?string $icon = null;

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
     * @return ServiceProvider
     */
    public function setName($name) {
        $this->name = $name;
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
     * @return ServiceProvider
     */
    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * @param string $url
     * @return ServiceProvider
     */
    public function setUrl($url) {
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