<?php

declare(strict_types=1);

namespace App\DarkMode;

use App\Entity\User;
use App\Repository\UserRepositoryInterface;
use SchulIT\CommonBundle\DarkMode\DarkModeManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class DarkModeManager implements DarkModeManagerInterface {

    private const string Key = 'dark_mode.enabled';

    public function __construct(private readonly TokenStorageInterface $tokenStorage, private readonly UserRepositoryInterface $userRepository)
    {
    }

    private function getUser(): ?User {
        $token = $this->tokenStorage->getToken();

        if (!$token instanceof TokenInterface) {
            return null;
        }

        $user = $token->getUser();

        if (!$user instanceof User) {
            return null;
        }

        return $user;
    }

    private function setDarkMode(bool $isDarkModeEnabled): void {
        $user = $this->getUser();

        if ($user instanceof User) {
            $user->setData(self::Key, $isDarkModeEnabled);
            $this->userRepository->persist($user);
        }
    }

    public function enableDarkMode(): void {
        $this->setDarkMode(true);
    }

    public function disableDarkMode(): void {
        $this->setDarkMode(false);
    }

    public function isDarkModeEnabled(): bool {
        $user = $this->getUser();

        if ($user instanceof User) {
            return $user->getData(self::Key, false) === true;
        }

        return false;
    }
}
