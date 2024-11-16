<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class ServiceAttributeUserRoleValue implements ServiceAttributeValueInterface {
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: ServiceAttribute::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ServiceAttribute $attribute;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: UserRole::class, inversedBy: 'attributes')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private UserRole $userRole;

    #[ORM\Column(type: 'object', nullable: true)]
    private string|array|int $value;

    public function getAttribute(): ServiceAttribute {
        return $this->attribute;
    }

    public function setAttribute(ServiceAttribute $attribute): self {
        $this->attribute = $attribute;
        return $this;
    }

    public function getUserRole(): UserRole {
        return $this->userRole;
    }

    public function setUserRole(UserRole $userRole): self {
        $this->userRole = $userRole;
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