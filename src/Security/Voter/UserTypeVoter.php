<?php

namespace App\Security\Voter;

use App\Entity\UserType;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserTypeVoter extends Voter {
    const REMOVE = 'remove';

    /**
     * @inheritDoc
     */
    protected function supports(string $attribute, $subject) {
        return $subject instanceof UserType
            && $attribute === static::REMOVE;
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token) {
        switch($attribute) {
            case static::REMOVE:
                return $this->canRemove($subject);
        }

        throw new LogicException('This code should not be executed.');
    }

    private function canRemove(UserType $userType) {
        return $userType->isBuiltIn() === false;
    }
}