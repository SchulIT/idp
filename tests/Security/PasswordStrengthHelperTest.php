<?php

namespace App\Tests\Security;

use App\Security\PasswordStrengthHelper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class PasswordStrengthHelperTest extends TestCase {
    public function testInvalidPasswords() {
        $invalidPasswords = [
            'ABc123$', // to short (but with all requirements)
            'abc123$$', // no upper/lowercase letters
            'abcdefg$', // no digits
            'abcDEF123', // no special chars
        ];

        $validator = Validation::createValidator();
        $helper = new PasswordStrengthHelper($validator);

        foreach($invalidPasswords as $invalidPassword) {
            $violations = $helper->validatePassword($invalidPassword);

            $this->assertGreaterThan(0, count($violations), sprintf("Password '%s' must not be valid", $invalidPassword));
        }
    }
}