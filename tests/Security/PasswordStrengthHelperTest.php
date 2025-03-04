<?php

namespace App\Tests\Security;

use App\Security\PasswordStrengthHelper;
use App\Settings\AppSettings;
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

        $settings = $this->createMock(AppSettings::class);
        $settings->isPasswordCompromisedCheckEnabled = true;

        $validator = Validation::createValidator();
        $helper = new PasswordStrengthHelper($settings, $validator);

        foreach($invalidPasswords as $invalidPassword) {
            $violations = $helper->validatePassword($invalidPassword);

            $this->assertGreaterThan(0, count($violations), sprintf("Password '%s' must not be valid", $invalidPassword));
        }
    }
}