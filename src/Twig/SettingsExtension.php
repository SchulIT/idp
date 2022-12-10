<?php

namespace App\Twig;

use App\Settings\SettingsManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SettingsExtension extends AbstractExtension {

    public function __construct(private SettingsManager $settingsManager)
    {
    }

    public function getFunctions(): array {
        return [
            new TwigFunction('setting', [ $this, 'setting' ])
        ];
    }

    public function setting($name, $default = null) {
        return $this->settingsManager->getValue($name, $default);
    }
}