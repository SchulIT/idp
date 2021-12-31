<?php

namespace App\Service;

use App\Repository\ServiceProviderRepositoryInterface;

class ServiceProviderTokenGenerator {

    private ServiceProviderRepositoryInterface $serviceProviderRepository;

    public function __construct(ServiceProviderRepositoryInterface $serviceProviderRepository) {
        $this->serviceProviderRepository = $serviceProviderRepository;
    }

    public function generateToken(): string {
        do {
            $token = hash('sha512', bin2hex(openssl_random_pseudo_bytes(128)));
        } while($this->serviceProviderRepository->findOneByToken($token) !== null);

        return $token;
    }
}