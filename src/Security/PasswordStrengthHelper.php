<?php

namespace App\Security;

use Rollerworks\Component\PasswordStrength\Validator\Constraints\PasswordRequirements;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PasswordStrengthHelper {
    private $validator;

    public function __construct(ValidatorInterface $validator) {
        $this->validator = $validator;
    }

    public function getConstraint(): Constraint {
        return new PasswordRequirements([
            'minLength' => 8,
            'requireLetters' => true,
            'requireCaseDiff' => true,
            'requireNumbers' => true,
            'requireSpecialCharacter' => true
        ]);
    }

    /**
     * @param string $password
     * @return ConstraintViolationListInterface
     */
    public function validatePassword(string $password): ConstraintViolationListInterface {
        $constraint = $this->getConstraint();
        return $this->validator->validate($password, $constraint);
    }
}