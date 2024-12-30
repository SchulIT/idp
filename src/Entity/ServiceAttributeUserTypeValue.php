<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class ServiceAttributeUserTypeValue implements ServiceAttributeValueInterface {
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: ServiceAttribute::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ServiceAttribute $attribute;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: UserType::class, inversedBy: 'attributes')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private UserType $userType;

    #[ORM\Column(type: 'json', nullable: true)]
    private mixed $value = null;

    public function getAttribute(): ServiceAttribute {
        return $this->attribute;
    }

    public function setAttribute(ServiceAttribute $attribute): self {
        $this->attribute = $attribute;
        return $this;
    }

    public function getUserType(): UserType {
        return $this->userType;
    }

    public function setUserType(UserType $userType): self {
        $this->userType = $userType;
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

}