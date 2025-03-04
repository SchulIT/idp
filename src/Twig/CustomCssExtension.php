<?php

namespace App\Twig;

use App\Settings\AppSettings;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CustomCssExtension extends AbstractExtension {

    public function __construct(private readonly AppSettings $settings) {}

    public function getFunctions(): array {
        return [
            new TwigFunction('customCSS', [$this, 'getCustomCSS']),
        ];
    }

    public function getCustomCSS(): ?string {
        return $this->settings->customCss;
    }
}