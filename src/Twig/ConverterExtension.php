<?php

namespace App\Twig;

use App\Converter\UserStringConverter;
use App\Entity\User;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ConverterExtension extends AbstractExtension {

    public function __construct(private UserStringConverter $userConverter)
    {
    }

    public function getFilters(): array {
        return [
            new TwigFilter('user', [ $this, 'user'])
        ];
    }

    public function user(User $user): string {
        return $this->userConverter->convert($user);
    }
}