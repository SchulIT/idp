<?php

namespace App\View\Filter;

use App\Entity\UserType;
use App\Repository\UserTypeRepositoryInterface;
use App\Utils\ArrayUtils;

class UserTypeFilter {
    public function __construct(private readonly UserTypeRepositoryInterface $userTypeRepository)
    {
    }

    public function handle($userType, ?UserType $defaultType = null) {
        $types = ArrayUtils::createArrayWithKeys($this->userTypeRepository->findAll(), fn(UserType $type) => (string)$type->getUuid());

        if($userType === null || is_numeric($userType)) {
            $type = $defaultType;
        } else {
            $type = $types[$userType] ?? $defaultType;
        }

        return new UserTypeFilterView($types, $type);
    }
}