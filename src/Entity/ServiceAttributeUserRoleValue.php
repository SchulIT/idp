<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class ServiceAttributeUserRoleValue implements ServiceAttributeValueInterface {
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: ServiceAttribute::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private $attribute;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: UserRole::class, inversedBy: 'attributes')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private $userRole;

    #[ORM\Column(type: 'object', nullable: true)]
    private $value;

    /**
     * @return ServiceAttribute
     */
    public function getAttribute() {
        return $this->attribute;
    }

    /**
     * @return ServiceAttributeUserRoleValue
     */
    public function setAttribute(ServiceAttribute $attribute) {
        $this->attribute = $attribute;
        return $this;
    }

    /**
     * @return UserRole
     */
    public function getUserRole() {
        return $this->userRole;
    }

    /**
     * @return ServiceAttributeUserRoleValue
     */
    public function setUserRole(UserRole $userRole) {
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
    public function setValue(string|array|int $value) {
        $this->value = $value;
    }
}