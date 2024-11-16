<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity]
class ServiceAttributeValue implements ServiceAttributeValueInterface {
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: ServiceAttribute::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ServiceAttribute $attribute;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'attributes')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private User $user;

    #[ORM\Column(type: 'object', nullable: true)]
    private string|int|array $value;
    
    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Gedmo\Timestampable(on: 'update', field: ['value'])]
    private ?DateTime $updatedAt;

    public function getAttribute(): ServiceAttribute {
        return $this->attribute;
    }

    public function setAttribute(ServiceAttribute $attribute): self {
        $this->attribute = $attribute;
        return $this;
    }

    public function getUser(): User {
        return $this->user;
    }

    public function setUser(User $user): self {
        $this->user = $user;
        return $this;
    }

    /**
     * @return string|string[]|int
     */
    public function getValue(): string|array|int {
        return $this->value;
    }

    /**
     * @param string|string[]|int $value
     */
    public function setValue(string|array|int $value): void {
        $this->value = $value;
    }

    public function getUpdatedAt(): ?DateTime {
        return $this->updatedAt;
    }
}