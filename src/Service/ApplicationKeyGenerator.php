<?php

namespace App\Service;

use App\Entity\Application;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Helper class for api key generation.
 */
class ApplicationKeyGenerator {

    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    /**
     * Generates a not used api key.
     *
     * @return string
     */
    public function generateApiKey() {
        do {
            $apiKey = bin2hex(openssl_random_pseudo_bytes(32));
            $application = $this->em->getRepository(Application::class)
                ->findOneByApiKey($apiKey);
        } while($application !== null);

        return $apiKey;
    }
}