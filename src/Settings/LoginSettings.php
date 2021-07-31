<?php

namespace App\Settings;

class LoginSettings extends AbstractSettings {
    public function getLoginMessage(): ?string {
        return $this->getValue('login.message', null);
    }

    public function setLoginMessage(?string $message): void {
        $this->setValue('login.message', $message);
    }
}