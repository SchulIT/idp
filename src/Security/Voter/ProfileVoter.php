<?php

declare(strict_types=1);

namespace App\Security\Voter;

use LogicException;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ProfileVoter extends Voter {

    public const CHANGE_PASSWORD = 'change_password';
    public const USE_2FA = 'use_2fa';

    /**
     * @inheritDoc
     */
    protected function supports($attribute, $subject): bool {
        return in_array($attribute, [ static::CHANGE_PASSWORD, static::USE_2FA]);
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token, Vote|null $vote = null): bool {
        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }
        return match ($attribute) {
            static::CHANGE_PASSWORD, static::USE_2FA => $user->canChangePassword(),
            default => throw new LogicException('This code should not be reached'),
        };
    }
}
