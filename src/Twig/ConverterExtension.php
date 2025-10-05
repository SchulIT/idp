<?php

declare(strict_types=1);

namespace App\Twig;

use App\Converter\UserStringConverter;
use App\Entity\User;
use Override;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ConverterExtension extends AbstractExtension {

    public function __construct(private readonly UserStringConverter $userConverter)
    {
    }

    #[Override]
    public function getFilters(): array {
        return [
            new TwigFilter('user', $this->user(...))
        ];
    }

    public function user(User $user): string {
        return $this->userConverter->convert($user);
    }
}
