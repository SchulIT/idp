<?php

namespace App\Setup;

use App\Entity\UserType;
use App\Repository\UserTypeRepositoryInterface;

class UserTypesSetup {
    public function __construct(private readonly UserTypeRepositoryInterface $userTypeRepository)
    {
    }

    /**
     * @return UserType[]
     */
    private function getDefaultUserTypes(): array {
        return [
            (new UserType())->setAlias('user')->setName('Benutzer')->setEduPerson(['member'])->setIsBuiltIn(true),
            (new UserType())->setAlias('student')->setName('Schülerin/Schüler')->setEduPerson(['student'])->setIsBuiltIn(true),
            (new UserType())->setAlias('parent')->setName('Elternteil')->setEduPerson(['affiliate'])->setIsBuiltIn(true),
            (new UserType())->setAlias('teacher')->setName('Lehrkraft')->setEduPerson(['faculty'])->setIsBuiltIn(true),
            (new UserType())->setAlias('secretary')->setName('Sekretariat')->setEduPerson(['staff'])->setIsBuiltIn(true)
        ];
    }

    /**
     * @return string[]
     */
    private function getExistingUserTypeAliases(): array {
        return array_map(fn(UserType $type) => $type->getAlias(), $this->userTypeRepository->findAll());
    }

    public function canSetup(): bool {
        $defaultUserTypes = $this->getDefaultUserTypes();
        $exisingUserTypeAliases = $this->getExistingUserTypeAliases();

        foreach($defaultUserTypes as $defaultUserType) {
            if(in_array($defaultUserType->getAlias(), $exisingUserTypeAliases) !== true) {
                return true;
            }
        }

        return false;
    }

    public function setupDefaultUserTypes() {
        $defaultUserTypes = $this->getDefaultUserTypes();
        $exisingUserTypeAliases = $this->getExistingUserTypeAliases();

        foreach($defaultUserTypes as $defaultUserType) {
            if(in_array($defaultUserType->getAlias(), $exisingUserTypeAliases) !== true) {
                $this->userTypeRepository->persist($defaultUserType);
            }
        }
    }
}