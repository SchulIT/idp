<?php

namespace App\Repository;

use App\Entity\PrivacyPolicy;
use Doctrine\ORM\EntityManagerInterface;

class PrivatePolicyRepository implements PrivacyPolicyRepositoryInterface {
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function findOne(): ?PrivacyPolicy {
        return $this->em->getRepository(PrivacyPolicy::class)
            ->findOneBy([]);
    }

    public function persist(PrivacyPolicy $policy): void {
        $this->em->persist($policy);
        $this->em->flush();
    }
}