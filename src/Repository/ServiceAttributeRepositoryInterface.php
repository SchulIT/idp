<?php

namespace App\Repository;

use App\Entity\ServiceAttribute;

interface ServiceAttributeRepositoryInterface {

    /**
     * @return ServiceAttribute[]
     */
    public function findAll();

    public function persist(ServiceAttribute $attribute);

    public function remove(ServiceAttribute $attribute);

    /**
     * @return ServiceAttribute[]
     * @deprecated
     */
    public function getAttributes();

    /**
     * @param string $entityId
     * @return ServiceAttribute[]
     */
    public function getAttributesForServiceProvider($entityId);
}