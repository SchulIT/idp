<?php

namespace App\Service;

use App\Entity\Application;
use App\Repository\ApplicationRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Helper class for api key generation.
 */
class ApplicationKeyGenerator {

    public function __construct(private ApplicationRepositoryInterface $repository)
    {
    }

    /**
     * Generates a not used api key.
     */
    public function generateApiKey(): string {
        do {
            $apiKey = bin2hex(openssl_random_pseudo_bytes(32));
            $application = $this->repository
                ->findOneByApiKey($apiKey);
        } while($application !== null);

        return $apiKey;
    }
}