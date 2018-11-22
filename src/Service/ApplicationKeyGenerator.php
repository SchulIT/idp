<?php

namespace App\Service;

use App\Entity\Application;
use App\Repository\ApplicationRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Helper class for api key generation.
 */
class ApplicationKeyGenerator {

    private $repository;

    public function __construct(ApplicationRepositoryInterface $repository) {
        $this->repository = $repository;
    }

    /**
     * Generates a not used api key.
     *
     * @return string
     */
    public function generateApiKey() {
        do {
            $apiKey = bin2hex(openssl_random_pseudo_bytes(32));
            $application = $this->repository
                ->findOneByApiKey($apiKey);
        } while($application !== null);

        return $apiKey;
    }
}