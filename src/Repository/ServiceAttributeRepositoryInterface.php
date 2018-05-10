<?php

namespace App\Repository;

use App\Entity\ServiceAttribute;

interface ServiceAttributeRepositoryInterface {
    /**
     * @return ServiceAttribute[]
     */
    public function getAttributes();

    /**
     * @param string $entityId
     * @return ServiceAttribute[]
     */
    public function getAttributesForServiceProvider($entityId);
}