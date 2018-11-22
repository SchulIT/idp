<?php

namespace App\Repository;

use App\Entity\ServiceProvider;

interface ServiceProviderRepositoryInterface {

    /**
     * @return ServiceProvider[]
     */
    public function findAll();

    public function persist(ServiceProvider $provider);
    
    public function remove(ServiceProvider $provider);

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


}