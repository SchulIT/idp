<?php

namespace App\Setup;

use App\Entity\UserType;
use App\Repository\UserTypeRepositoryInterface;

class UserTypesSetup {
    private $userTypeRepository;

    public function __construct(UserTypeRepositoryInterface $userTypeRepository) {
        $this->userTypeRepository = $userTypeRepository;
    }

    /**
     * @return UserType[]
     */
    private function getDefaultUserTypes() {
        return [
            (new UserType())->setAlias('student')->setName('Schülerin/Schüler')->setEduPerson(['student']),
            (new UserType())->setAlias('parent')->setName('Elternteil')->setEduPerson(['affiliate']),
            (new UserType())->setAlias('teacher')->setName('Lehrkraft')->setEduPerson(['faculty']),
            (new UserType())->setAlias('secretary')->setName('Sekretariat')->setEduPerson(['staff'])
        ];
    }

    /**
     * @return string[]
     */
    private function getExistingUserTypeAliases() {
        return array_map(function(UserType $type) {
            return $type->getAlias();
        }, $this->userTypeRepository->findAll());
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