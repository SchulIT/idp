<?php

namespace App\User\Bulk;

use App\Entity\User;
use App\Repository\UserRepository;
use Override;

readonly class RemoveAction implements BulkActionInterface {
    public function __construct(
        private UserRepository $userRepository,
    ) {

    }

    #[Override]
    public function performAction(User $user, mixed $parameter = null): void {
        $this->userRepository->remove($user);
    }

    #[Override]
    public function getKey(): string {
        return 'remove_user';
    }

    #[Override]
    public function getMessageTranslationKey(): string {
        return 'users.bulk.actions.remove_user';
    }
}