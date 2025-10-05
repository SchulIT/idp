<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\User;
use App\Message\MustProvisionUser;
use App\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsMessageHandler]
class MustProvisionUserHandler {

    public function __construct(private readonly UserRepositoryInterface $userRepository, private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function __invoke(MustProvisionUser $message): void {
        $user = $this->userRepository->findOneById($message->getUserId());

        if($user instanceof User) {
            $user->setPassword(
                $this->passwordHasher->hashPassword($user, $user->getPassword())
            );

            $user->setIsProvisioned(true);
            $this->userRepository->persist($user);
        }
        // if user was not found -> discard message
    }
}
