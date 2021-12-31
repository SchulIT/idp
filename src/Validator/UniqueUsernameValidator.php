<?php

namespace App\Validator;

use App\Entity\ActiveDirectoryUser;
use App\Entity\User;
use App\Repository\UserRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueUsernameValidator extends ConstraintValidator {

    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository) {
        $this->userRepository = $userRepository;
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint) {
        if(!$constraint instanceof UniqueUsername) {
            throw new UnexpectedTypeException($constraint, UniqueUsername::class);
        }

        if(!is_string($value)) {
            throw new UnexpectedTypeException($constraint, 'string');
        }

        $user = $this->userRepository->findOneByUsername($value);

        if($user !== null && $this->matchesType($user, $constraint->type) === false) {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation();
        }
    }

    private function matchesType(User $user, string $type) {
        return (get_class($user) === User::class && $type === 'user')
            || (get_class($user) === ActiveDirectoryUser::class && $type === 'ad');
    }
}