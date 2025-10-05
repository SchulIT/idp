<?php

declare(strict_types=1);

namespace App\View\Filter;

use App\Entity\UserType;
use App\Repository\UserTypeRepositoryInterface;
use App\Utils\ArrayUtils;

class UserTypeFilter {
    public function __construct(private readonly UserTypeRepositoryInterface $userTypeRepository)
    {
    }

    public function handle($userType, ?UserType $defaultType = null): \App\View\Filter\UserTypeFilterView {
        $types = ArrayUtils::createArrayWithKeys($this->userTypeRepository->findAll(), fn(UserType $type): string => (string)$type->getUuid());

        $type = $userType === null || is_numeric($userType) ? $defaultType : $types[$userType] ?? $defaultType;

        return new UserTypeFilterView($types, $type);
    }
}
