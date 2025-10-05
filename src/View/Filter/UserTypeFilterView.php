<?php

declare(strict_types=1);

namespace App\View\Filter;

use App\Entity\UserType;

class UserTypeFilterView {

    private bool $handleNull = false;

    /**
     * @param UserType[] $types
     */
    public function __construct(private readonly array $types, private readonly ?UserType $currentType)
    {
    }

    /**
     * @return UserType[]
     */
    public function getTypes(): array {
        return $this->types;
    }

    public function getCurrentType(): ?UserType {
        return $this->currentType;
    }

    public function getHandleNull(): bool {
        return $this->handleNull;
    }

    public function setHandleNull(bool $handleNull): UserTypeFilterView {
        $this->handleNull = $handleNull;
        return $this;
    }

}
