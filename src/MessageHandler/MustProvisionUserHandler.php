<?php

namespace App\MessageHandler;

use App\Message\MustProvisionUser;
use App\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsMessageHandler]
class MustProvisionUserHandler {

    public function __construct(private UserRepositoryInterface $userRepository, private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function __invoke(MustProvisionUser $message): void {
        $user = $this->userRepository->findOneById($message->getUserId());

        if($user !== null) {
            $user->setPassword(
                $this->passwordHasher->hashPassword($user, $user->getPassword())
            );

            $user->setIsProvisioned(true);
            $this->userRepository->persist($user);
        }
        // if user was not found -> discard message
    }
}