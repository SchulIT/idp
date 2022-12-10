<?php

namespace App\Twig;

use App\Entity\User;
use App\Service\UserServiceProviderResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserVariable {
    public function __construct(private TokenStorageInterface $tokenStorage, private UserServiceProviderResolver $serviceProviderResolver)
    {
    }

    public function getUser(): ?User {
        $token = $this->tokenStorage->getToken();

        if($token === null) {
            return null;
        }

        $user = $token->getUser();

        if(!$user instanceof User) {
            return null;
        }

        return $user;
    }

    public function getStudentId(): ?string {
        return $this->getUser()->getExternalId();
    }

    public function getFirstname(): ?string {
        return $this->getUser()->getFirstname();
    }

    public function getLastname(): ?string {
        return $this->getUser()->getLastname();
    }

    public function getEmailAddress(): ?string {
        return $this->getUser()->getEmail();
    }

    public function getServices() {
        return $this->serviceProviderResolver->getServicesForCurrentUser();
    }
}