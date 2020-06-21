<?php

namespace App\Converter;

use App\Entity\User;

class UserStringConverter {
    public function convert(User $user) {
        if(empty($user->getLastname()) && empty($user->getFirstname())) {
            return $user->getUsername();
        } else if(empty($user->getFirstname())) {
            return sprintf('%s (%s)', $user->getFirstname(), $user->getUsername());
        } else if(empty($user->getLastname())) {
            return sprintf('%s (%s)', $user->getLastname(), $user->getUsername());
        }

        return sprintf('%s, %s (%s)', $user->getLastname(), $user->getFirstname(), $user->getUsername());
    }
}