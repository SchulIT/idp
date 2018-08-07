<?php

namespace App\Security\Voter;

use App\Entity\U2fKey;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class U2fKeyVoter extends Voter {

    const REMOVE = 'remove';

    /**
     * @inheritDoc
     */
    protected function supports($attribute, $subject) {
        return $subject instanceof U2fKey && $attribute === static::REMOVE;
    }

    /**
     * @inheritDoc
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token) {
        switch($attribute) {
            case static::REMOVE:
                return $this->canRemove($subject, $token);
        }

        throw new \LogicException('This code should not be reached');
    }

    private function canRemove(U2fKey $key, TokenInterface $token) {
        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        if($key->getUser() === null) {
            return false;
        }

        return $key->getUser()->getId() === $user->getId();
    }
}