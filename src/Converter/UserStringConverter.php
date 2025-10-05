<?php

declare(strict_types=1);

namespace App\Converter;

use App\Entity\User;

class UserStringConverter {
    public function convert(User $user): string {
        if (in_array($user->getLastname(), [null, '', '0'], true) && in_array($user->getFirstname(), [null, '', '0'], true)) {
            return $user->getUsername();
        } elseif (in_array($user->getFirstname(), [null, '', '0'], true)) {
            return sprintf('%s (%s)', $user->getFirstname(), $user->getUsername());
        } elseif (in_array($user->getLastname(), [null, '', '0'], true)) {
            return sprintf('%s (%s)', $user->getLastname(), $user->getUsername());
        }

        return sprintf('%s, %s (%s)', $user->getLastname(), $user->getFirstname(), $user->getUsername());
    }
}
