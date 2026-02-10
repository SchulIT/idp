<?php

namespace App\User\Bulk;

use App\Entity\User;
use App\Repository\UserRepositoryInterface;
use App\Repository\UserRoleRepositoryInterface;
use Override;

readonly class RemoveUserRoleAction implements BulkActionInterface {

    public function __construct(
        private UserRepositoryInterface $userRepository,
        private UserRoleRepositoryInterface $userRoleRepository,
    ) {

    }

    #[Override]
    public function performAction(User $user, mixed $parameter = null): void {
        if(!is_string($parameter)) {
            return;
        }

        $role = $this->userRoleRepository->findOneByUuid($parameter);

        if($role === null) {
            return;
        }

        if($user->getUserRoles()->contains($role)) {
            $user->removeUserRole($role);
            $this->userRepository->persist($user);
        }
    }

    #[Override]
    public function getKey(): string {
        return 'remove_user_role';
    }

    #[Override]
    public function getMessageTranslationKey(): string {
        return 'users.bulk.actions.remove_user_role';
    }
}