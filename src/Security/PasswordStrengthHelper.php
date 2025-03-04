<?php

namespace App\Security;

use App\Settings\AppSettings;
use Rollerworks\Component\PasswordStrength\Validator\Constraints\PasswordRequirements;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PasswordStrengthHelper {
    public function __construct(private readonly AppSettings $settings, private readonly ValidatorInterface $validator)
    {
    }

    public function getConstraints(): array {
        $constraints = [
            new PasswordRequirements([
                'minLength' => 8,
                'requireLetters' => true,
                'requireCaseDiff' => true,
                'requireNumbers' => true,
                'requireSpecialCharacter' => true
            ])
        ];

        if($this->settings->isPasswordCompromisedCheckEnabled) {
            $constraints[] = new NotCompromisedPassword();
        }
        
        return $constraints;
    }

    public function validatePassword(string $password): ConstraintViolationListInterface {
        $constraints = $this->getConstraints();
        return $this->validator->validate($password, $constraints);
    }
}