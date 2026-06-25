<?php

namespace App\User\Bulk;

use App\Entity\User;
use App\Repository\UserRepositoryInterface;
use Override;
use Symfony\Component\HttpFoundation\Request;

readonly class SetGradeAction implements BulkActionInterface {

    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) { }

    #[Override]
    public function performAction(User $user, Request $request): void {
        $parameter = $request->request->get('grade');

        if(in_array('student', $user->getType()->getEduPerson())) {
            $user->setGrade($parameter);
        }

        $this->userRepository->persist($user);
    }

    #[Override]
    public function getKey(): string {
        return 'set_grade';
    }

    #[Override]
    public function getMessageTranslationKey(): string {
        return 'users.bulk.actions.set_grade';
    }
}