<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ProfileVoter extends Voter {

    const CHANGE_PASSWORD = 'change_password';
    const USE_2FA = 'use_2fa';

    /**
     * @inheritDoc
     */
    protected function supports($attribute, $subject): bool {
        return in_array($attribute, [ static::CHANGE_PASSWORD, static::USE_2FA]);
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool {
        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        switch($attribute) {
            case static::CHANGE_PASSWORD:
            case static::USE_2FA:
                return $user->canChangePassword();
        }

        throw new \LogicException('This code should not be reached');
    }
}