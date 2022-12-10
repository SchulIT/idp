<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity]
class ServiceAttributeValue implements ServiceAttributeValueInterface {
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: 'ServiceAttribute')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private $attribute;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: 'User', inversedBy: 'attributes')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private $user;

    #[ORM\Column(type: 'object', nullable: true)]
    private $value;

    /**
     * @Gedmo\Timestampable(on="update", field={"value"})
     */
    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updatedAt;

    /**
     * @return ServiceAttribute
     */
    public function getAttribute() {
        return $this->attribute;
    }

    /**
     * @return ServiceAttributeValue
     */
    public function setAttribute(ServiceAttribute $attribute) {
        $this->attribute = $attribute;
        return $this;
    }

    /**
     * @return User
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * @return ServiceAttributeValue
     */
    public function setUser(User $user) {
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
    public function setValue(string|array|int $value) {
        $this->value = $value;
    }

    public function getUpdatedAt(): ?DateTime {
        return $this->updatedAt;
    }
}