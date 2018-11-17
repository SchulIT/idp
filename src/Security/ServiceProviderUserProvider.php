<?php

namespace App\Security;

use App\Entity\ServiceProvider;
use App\Repository\ServiceProviderRepositoryInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ServiceProviderUserProvider implements UserProviderInterface {

    private $serviceProviderRepository;

    public function __construct(ServiceProviderRepositoryInterface $serviceProviderRepository) {
        $this->serviceProviderRepository = $serviceProviderRepository;
    }

    public function loadUserByToken($token): ?ServiceProvider {
        return $this->serviceProviderRepository
            ->findOneByToken($token);
    }

    /**
     * @inheritDoc
     */
    public function loadUserByUsername($entityId) {
        return $this->serviceProviderRepository
            ->findOneByEntityId($entityId);
    }

    /**
     * @inheritDoc
     */
    public function refreshUser(UserInterface $user) {
        throw new UnsupportedUserException();
    }

    /**
     * @inheritDoc
     */
    public function supportsClass($class) {
        return $class === ServiceProvider::class;
    }
}