<?php

namespace App\Validator;

use App\Repository\UserTypeRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ValidUserTypeUuidValidator extends ConstraintValidator {

    public function __construct(private UserTypeRepositoryInterface $userTypeRepository)
    {
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint): void {
        if(!$constraint instanceof ValidUserTypeUuid) {
            throw new UnexpectedTypeException($constraint, ValidUserTypeUuid::class);
        }

        if($this->userTypeRepository->findOneByUuid($value) === null) {
            $this->context
                ->buildViolation($constraint->message)
                ->setParameter('{{ uuid }}', $value)
                ->addViolation();
        }
    }
}