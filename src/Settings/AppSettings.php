<?php

namespace App\Settings;

class AppSettings extends AbstractSettings {
    public function getHelpdeskMail(): ?string {
        return $this->getValue('helpdesk.mail');
    }

    public function setHelpdeskInfo(?string $info): void {
        $this->setValue('helpdesk.info', $info);
    }

    public function isPasswordCompromisedCheckEnabled(): bool {
        return $this->getValue('security.password_compromised_check', true);
    }

    public function setPasswordCompromisedCheckEnabled(bool $enabled): void {
        $this->setValue('security.password_compromised_check', $enabled);
    }

}