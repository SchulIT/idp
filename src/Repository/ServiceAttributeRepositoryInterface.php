<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ServiceAttribute;
use App\Entity\ServiceProvider;

interface ServiceAttributeRepositoryInterface {

    /**
     * @return ServiceAttribute[]
     */
    public function findAll(): array;

    public function persist(ServiceAttribute $attribute): void;

    public function remove(ServiceAttribute $attribute): void;

    /**
     * @return ServiceAttribute[]
     * @deprecated Use findAll();
     */
    public function getAttributes(): array;

    /**
     * @param string $entityId
     * @return ServiceAttribute[]
     */
    public function getAttributesForSamlServiceProvider($entityId): array;

    /**
     * @param ServiceProvider $serviceProvider
     * @return ServiceAttribute[]
     */
    public function getAttributesForServiceProvider(ServiceProvider $serviceProvider): array;
}
