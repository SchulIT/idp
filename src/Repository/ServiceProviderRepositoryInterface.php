<?php

namespace App\Repository;

use App\Entity\SamlServiceProvider;
use App\Entity\ServiceProvider;

interface ServiceProviderRepositoryInterface {

    /**
     * @return ServiceProvider[]
     */
    public function findAll(): array;

    public function persist(ServiceProvider $provider): void;
    
    public function remove(ServiceProvider $provider): void;

    /**
     * @param string $token
     * @return ServiceProvider|null
     */
    public function findOneByToken(string $token): ?ServiceProvider;

    /**
     * @param string $entityId
     * @return SamlServiceProvider|null
     */
    public function findOneByEntityId(string $entityId): ?SamlServiceProvider;


}