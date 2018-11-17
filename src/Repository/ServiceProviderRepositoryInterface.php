<?php

namespace App\Repository;

use App\Entity\ServiceProvider;

interface ServiceProviderRepositoryInterface {

    /**
     * @param string $token
     * @return ServiceProvider|null
     */
    public function findOneByToken(string $token): ?ServiceProvider;

    /**
     * @param string $entityId
     * @return ServiceProvider|null
     */
    public function findOneByEntityId(string $entityId): ?ServiceProvider;

    /**
     * @return ServiceProvider[]
     */
    public function findAll();
}