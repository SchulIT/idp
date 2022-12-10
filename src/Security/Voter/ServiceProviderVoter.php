<?php

namespace App\Security\Voter;

use App\Entity\SamlServiceProvider;
use App\Entity\ServiceProvider;
use App\Entity\User;
use App\Service\UserServiceProviderResolver;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ServiceProviderVoter extends Voter {

    public const ENABLED = 'enabled';

    public function __construct(private UserServiceProviderResolver $userServiceProviderResolver)
    {
    }

    protected function supports($attribute, $subject): bool {
        return $attribute === static::ENABLED
            && $subject instanceof SamlServiceProvider;
    }

    /**
     * @param ServiceProvider $subject
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool {
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