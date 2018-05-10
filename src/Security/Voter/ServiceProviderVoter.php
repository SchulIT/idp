<?php

namespace App\Security\Voter;

use App\Entity\ServiceProvider;
use App\Service\UserServiceProviderResolver;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ServiceProviderVoter extends Voter {

    const ENABLED = 'enabled';

    private $userServiceProviderResolver;

    public function __construct(UserServiceProviderResolver $userServiceProviderResolver) {
        $this->userServiceProviderResolver = $userServiceProviderResolver;
    }

    protected function supports($attribute, $subject) {
        return $attribute === static::ENABLED
            && $subject instanceof ServiceProvider;
    }

    /**
     * @param string $attribute
     * @param ServiceProvider $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token) {
        if($subject === null) {
            return false;
        }

        $user = $token->getUser();

        /** @var ServiceProvider[] $services */
        $services = $this->userServiceProviderResolver->getServices($user);

        foreach($services as $service) {
            if($service->getEntityId() === $subject->getEntityId()) {
                return true;
            }
        }

        return false;
    }
}