<?php

namespace App\Twig;

use App\Converter\UserStringConverter;
use App\Entity\User;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ConverterExtension extends AbstractExtension {

    private $userConverter;

    public function __construct(UserStringConverter $userConverter) {
        $this->userConverter = $userConverter;
    }

    public function getFilters() {
        return [
            new TwigFilter('user', [ $this, 'user'])
        ];
    }

    public function user(User $user) {
        return $this->userConverter->convert($user);
    }
}