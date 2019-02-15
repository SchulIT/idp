<?php

namespace App\Twig;

use App\Entity\User;
use App\Service\UserServiceProviderResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserVariable {
    private $tokenStorage;
    private $serviceProviderResolver;

    public function __construct(TokenStorageInterface $tokenStorage, UserServiceProviderResolver $serviceProviderResolver) {
        $this->tokenStorage = $tokenStorage;
        $this->serviceProviderResolver = $serviceProviderResolver;
    }

    /**
     * @return User
     */
    public function getUser(): User {
        $token = $this->tokenStorage->getToken();

        if($token === null) {
            return null;
        }

        $user = $token->getUser();

        if($user === null || !$user instanceof User) {
            return null;
        }

        return $user;
    }

    public function getStudentId() {
        return $this->getUser()->getInternalId();
    }

    public function getFirstname() {
        return $this->getUser()->getFirstname();
    }

    public function getLastname() {
        return $this->getUser()->getLastname();
    }

    public function getEmailAddress() {
        return $this->getUser()->getEmail();
    }

    public function getServices() {
        return $this->serviceProviderResolver->getServicesForCurrentUser();
    }
}