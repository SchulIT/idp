<?php

namespace App\Repository;

use App\Entity\ServiceProvider;
use App\Entity\ServiceProviderConfirmation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class ServiceProviderConfirmationRepository implements ServiceProviderConfirmationRepositoryInterface {

    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function findOneByUserAndServiceProvider(User $user, ServiceProvider $serviceProvider): ?ServiceProviderConfirmation {
        return $this->em->getRepository(ServiceProviderConfirmation::class)
            ->findOneBy([
                'user' => $user,
                'serviceProvider' => $serviceProvider
            ]);
    }

    public function persist(ServiceProviderConfirmation $confirmation): void {
        $this->em->persist($confirmation);
        $this->em->flush();
    }
}