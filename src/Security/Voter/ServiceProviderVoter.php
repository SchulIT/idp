<?php

namespace App\Security\Voter;

use App\Entity\SamlServiceProvider;
use App\Entity\ServiceProvider;
use App\Entity\User;
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
            && $subject instanceof SamlServiceProvider;
    }

    /**
     * @param string $attribute
     * @param ServiceProvider $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token) {
        if(!$subject instanceof SamlServiceProvider) {
            return true;
        }

        $user = $token->getUser();

        if(!$user instanceof User) {
            return false;
        }

        /** @var ServiceProvider[] $services */
        $services = $this->userServiceProviderResolver->getServices($user);

        foreach($services as $service) {
            if($service instanceof SamlServiceProvider && $service->getEntityId() === $subject->getEntityId()) {
                return true;
            }
        }

        return false;
    }
}