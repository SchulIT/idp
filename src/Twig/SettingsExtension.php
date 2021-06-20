<?php

namespace App\Twig;

use App\Settings\SettingsManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SettingsExtension extends AbstractExtension {

    private $settingsManager;

    public function __construct(SettingsManager $settingsManager) {
        $this->settingsManager = $settingsManager;
    }

    public function getFunctions() {
        return [
            new TwigFunction('setting', [ $this, 'setting' ])
        ];
    }

    public function setting($name, $default = null) {
        return $this->settingsManager->getValue($name, $default);
    }
}