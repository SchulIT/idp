<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\UserType;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserTypeVoter extends Voter {
    public const REMOVE = 'remove';

    /**
     * @inheritDoc
     */
    protected function supports(string $attribute, $subject): bool {
        return $subject instanceof UserType
            && $attribute === static::REMOVE;
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token, Vote|null $vote = null): bool
    {
        return match ($attribute) {
            static::REMOVE => $this->canRemove($subject),
            default => throw new LogicException('This code should not be executed.'),
        };
    }

    private function canRemove(UserType $userType): bool {
        return $userType->isBuiltIn() === false;
    }
}
