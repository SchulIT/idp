<?php

namespace App\Repository;

use App\Entity\ServiceProvider;
use App\Entity\ServiceProviderConfirmation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class ServiceProviderConfirmationRepository implements ServiceProviderConfirmationRepositoryInterface {

    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    public function findOneByUserAndServiceProvider(User $user, ServiceProvider $serviceProvider): ?ServiceProviderConfirmation {
        return $this->em->getRepository(ServiceProviderConfirmation::class)
            ->findOneBy([
                'user' => $user,
                'serviceProvider' => $serviceProvider
            ]);
    }

    public function persist(ServiceProviderConfirmation $confirmation) {
        $this->em->persist($confirmation);
        $this->em->flush();
    }
}