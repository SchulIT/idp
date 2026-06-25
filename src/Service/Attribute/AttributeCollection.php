<?php

namespace App\Service\Attribute;

use App\Entity\ServiceAttribute;
use App\Entity\ServiceAttributeUserRoleValue;
use App\Entity\ServiceAttributeUserTypeValue;
use App\Entity\ServiceAttributeValue;

class AttributeCollection {

    public ServiceAttributeValue|null $userValue = null;
    public ServiceAttributeUserTypeValue|null $userTypeValue = null;

    /** @var ServiceAttributeUserRoleValue[] */
    private array $userRoleValues = [];

    public function __construct(
        public readonly ServiceAttribute $attribute
    ) { }

    public function addUserRoleValue(ServiceAttributeUserRoleValue $userRoleValue): void {
        $this->userRoleValues[] = $userRoleValue;

        usort($this->userRoleValues, fn(ServiceAttributeUserRoleValue $userRoleValue1, ServiceAttributeUserRoleValue $userRoleValue2): int => $userRoleValue1 <=> $userRoleValue2);
    }

    public function getUserRoleValues(): array {
        return $this->userRoleValues;
    }
}