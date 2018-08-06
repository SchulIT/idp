<?php

namespace App\Security;

use App\Entity\Application;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ApplicationUserProvider implements UserProviderInterface {

    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    public function loadUserByApiKey($apiKey): ?Application {
        return $this->em
            ->getRepository(Application::class)
            ->findOneByApiKey($apiKey);
    }

    /**
     * @inheritDoc
     */
    public function loadUserByUsername($username) {
        return $this->em
            ->getRepository(Application::class)
            ->findOneByUsername($username);
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