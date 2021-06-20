<?php

namespace App\Settings;

class RegistrationSettings extends AbstractSettings {
    public function getUsernameSuffix(): string {
        return $this->getValue('registration.suffix', 'e.schulit.de');
    }

    public function setUsernameSuffix(string $suffix): void {
        $this->setValue('registration.suffix', $suffix);
    }

    
}