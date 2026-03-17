<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class JsonExtension extends AbstractExtension {
    public function getFilters(): array {
        return [
            new TwigFilter('json_decode', $this->jsonDecode(...)),
        ];
    }

    public function jsonDecode(string|null $json): array|object|null {
        if(empty($json)) {
            return null;
        }

        return json_decode($json, true);
    }
}