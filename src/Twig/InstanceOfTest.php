<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigTest;

class InstanceOfTest extends AbstractExtension {
    public function getTests(): array {
        return [
            new TwigTest('instanceof', [$this, 'instanceof']),
        ];
    }

    public function instanceOf($object, string $className): bool {
        return $object instanceof $className;
    }
}