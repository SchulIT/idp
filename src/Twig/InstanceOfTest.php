<?php

declare(strict_types=1);

namespace App\Twig;

use Override;
use Twig\Extension\AbstractExtension;
use Twig\TwigTest;

class InstanceOfTest extends AbstractExtension {
    #[Override]
    public function getTests(): array {
        return [
            new TwigTest('instanceof', $this->instanceof(...)),
        ];
    }

    public function instanceOf($object, string $className): bool {
        return $object instanceof $className;
    }
}
