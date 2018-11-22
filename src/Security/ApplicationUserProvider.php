<?php

namespace App\Security;

use App\Entity\Application;
use App\Repository\ApplicationRepositoryInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ApplicationUserProvider implements UserProviderInterface {

    private $repository;

    public function __construct(ApplicationRepositoryInterface $repository) {
        $this->repository = $repository;
    }

    public function loadUserByApiKey($apiKey): ?Application {
        return $this->repository
            ->findOneByApiKey($apiKey);
    }

    /**
     * @inheritDoc
     */
    public function loadUserByUsername($apiKey) {
        return $this->repository
            ->findOneByApiKey($apiKey);
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
        return $class === Application::class;
    }
}