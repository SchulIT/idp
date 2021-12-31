<?php

namespace App\Security;

use App\Settings\AppSettings;
use Rollerworks\Component\PasswordStrength\Validator\Constraints\PasswordRequirements;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PasswordStrengthHelper {
    private AppSettings $settings;
    private ValidatorInterface $validator;

    public function __construct(AppSettings $settings, ValidatorInterface $validator) {
        $this->settings = $settings;
        $this->validator = $validator;
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

        if($this->settings->isPasswordCompromisedCheckEnabled()) {
            $constraints[] = new NotCompromisedPassword();
        }
        
        return $constraints;
    }

    /**
     * @param string $password
     * @return ConstraintViolationListInterface
     */
    public function validatePassword(string $password): ConstraintViolationListInterface {
        $constraints = $this->getConstraints();
        return $this->validator->validate($password, $constraints);
    }
}