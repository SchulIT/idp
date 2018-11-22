<?php

namespace App\Repository;

use App\Entity\Application;

interface ApplicationRepositoryInterface {

    /**
     * @return Application[]
     */
    public function findAll();

    public function findOneByApiKey($key): ?Application;

    public function persist(Application $application);

    public function remove(Application $application);
}