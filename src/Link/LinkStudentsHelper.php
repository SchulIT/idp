<?php

namespace App\Link;

use App\Entity\User;
use App\Repository\UserRepositoryInterface;

class LinkStudentsHelper {

    public const StudentEduPerson = 'student';

    private $repository;

    public function __construct(UserRepositoryInterface $userRepository) {
        $this->repository = $userRepository;
    }

    /**
     * Establishes a link between the current user and a student user
     *
     * @param User $currentUser
     * @param User $student
     * @throws NotAStudentException
     */
    public function link(User $currentUser, User $student) {
        $studentEduPerson = $student->getType()->getEduPerson();

        if(!in_array(static::StudentEduPerson, $studentEduPerson)) {
            throw new NotAStudentException();
        }

        $ids = [ ];
        if(!empty($currentUser->getExternalId())) {
            $ids = explode(',', $currentUser->getExternalId());
        }

        $ids[] = $student->getExternalId();
        $currentUser->setExternalId(implode(',', $ids));

        $this->repository->persist($currentUser);
    }

    /**
     * Returns all linked student users
     *
     * @param User $user
     * @return User[]
     */
    public function getLinks(User $user): array {
        $ids = explode(',', $user->getExternalId());
        $users = [ ];

        foreach($ids as $id) {
            $user = $this->repository->findOneByExternalId($id);

            if($user !== null) {
                $users[] = $user;
            }
        }

        return $users;
    }
}