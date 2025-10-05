<?php

declare(strict_types=1);

namespace App\Security;

use App\ActiveDirectory\OptionResolver;
use App\Entity\ActiveDirectoryGradeSyncOption;
use App\Entity\ActiveDirectoryRoleSyncOption;
use App\Entity\ActiveDirectorySyncOption;
use App\Entity\ActiveDirectoryUser;
use App\Entity\User;
use App\Entity\UserType;
use App\Repository\ActiveDirectoryGradeSyncOptionRepositoryInterface;
use App\Repository\ActiveDirectoryRoleSyncOptionRepositoryInterface;
use App\Repository\ActiveDirectorySyncOptionRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use Ramsey\Uuid\Uuid;

/**
 * Helper which creates users after successful Active Directory authentication.
 */
class UserCreator {
    /** @var ActiveDirectorySyncOption[] */
    private ?array $syncOptions = null;

    /** @var ActiveDirectoryGradeSyncOption[] */
    private ?array $gradeSyncOptions = null;

    /** @var ActiveDirectoryRoleSyncOption[] */
    private ?array $roleSyncOptions = null;

    public function __construct(private readonly ActiveDirectorySyncOptionRepositoryInterface $syncOptionRepository, private readonly ActiveDirectoryGradeSyncOptionRepositoryInterface $gradeSyncOptionRepository, private readonly ActiveDirectoryRoleSyncOptionRepositoryInterface $roleSyncOptionRepository, private readonly OptionResolver $optionsResolver, private readonly UserRepositoryInterface $userRepository)
    {
    }

    private function initialize(): void {
        if ($this->syncOptions === null) {
            $this->syncOptions = $this->syncOptionRepository
                ->findAll();
        }

        if ($this->gradeSyncOptions === null) {
            $this->gradeSyncOptions = $this->gradeSyncOptionRepository
                ->findAll();
        }

        if($this->roleSyncOptions === null) {
            $this->roleSyncOptions = $this->roleSyncOptionRepository
                ->findAll();
        }
    }

    /**
     * Determines whether the user can be imported from Active Directory.
     */
    public function canCreateUser(ActiveDirectoryUserInformation $response): bool {
        $this->initialize();
        return $this->getTargetUserType($response) instanceof UserType;
    }

    private function getTargetUserType(ActiveDirectoryUserInformation $response): ?UserType {
        /** @var ActiveDirectorySyncOption|null $option */
        $option = $this->optionsResolver
            ->getOption($this->syncOptions, $response->getOu(), $response->getGroups());

        if ($option !== null) {
            return $option->getUserType();
        }

        return null;
    }

    /**
     * @param ActiveDirectoryUser|null $user Already existing AD user
     * @return ActiveDirectoryUser
     */
    public function createUser(ActiveDirectoryUserInformation $response, ?ActiveDirectoryUser $user = null): ?ActiveDirectoryUser {
        $this->initialize();

        if (!$user instanceof ActiveDirectoryUser) {
            // Try to find already existing user by GUID (because username has changed)
            $user = $this->userRepository->findActiveDirectoryUserByObjectGuid($response->getGuid());

            if(!$user instanceof ActiveDirectoryUser) {
                $user = new ActiveDirectoryUser();
                $user->setObjectGuid(Uuid::fromString($response->getGuid()));
            }
        }

        $user->setUsername(mb_strtolower($response->getUserPrincipalName()));
        $user->setFirstname($response->getFirstname());
        $user->setLastname($response->getLastname());
        $user->setGrade($this->getGrade($response));
        $user->setUserPrincipalName(mb_strtolower($response->getUserPrincipalName()));
        $user->setType($this->getTargetUserType($response));
        $user->setEmail($response->getEmail());
        $user->setExternalId($response->getUniqueId());

        $user->setOu($response->getOu());
        $user->setGroups($response->getGroups());

        // Set roles

        /** @var ActiveDirectoryRoleSyncOption[] $options */
        $options = $this->optionsResolver->getAllOptions(
            $this->roleSyncOptions,
            $response->getOu(),
            $response->getGroups()
        );

        foreach($options as $option) {
            $role = $option->getUserRole();

            if($user->getUserRoles()->contains($role) !== true) {
                $user->addUserRole($role);
            }
        }

        $existingUser = $this->userRepository->findOneByUsername($user->getUserPrincipalName());

        // Convert user (if necessary)
        if($existingUser instanceof User && !$existingUser instanceof ActiveDirectoryUser) {
            $user = $this->userRepository->convertToActiveDirectory($existingUser, $user);
        }

        return $user;
    }

    private function getGrade(ActiveDirectoryUserInformation $response): ?string {
        /** @var ActiveDirectoryGradeSyncOption|null $option */
        $option = $this->optionsResolver
            ->getOption($this->gradeSyncOptions, $response->getOu(), $response->getGroups());

        if($option !== null) {
            return $option->getGrade();
        }

        return null;
    }
}
