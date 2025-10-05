<?php

declare(strict_types=1);

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

    public function findOneByEntityId(string $entityId): ?SamlServiceProvider;


}
