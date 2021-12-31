<?php

namespace App\Settings;

abstract class AbstractSettings {
    private SettingsManager $settingsManager;

    public function __construct(SettingsManager $settingsManager) {
        $this->settingsManager = $settingsManager;
    }

    protected function getValue(string $key, $default = null) {
        return $this->settingsManager
            ->getValue($key, $default);
    }

    protected function setValue(string $key, $value): void {
        $this->settingsManager
            ->setValue($key, $value);
    }
}