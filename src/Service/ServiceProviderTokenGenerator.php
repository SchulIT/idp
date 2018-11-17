<?php

namespace App\Service;

class ServiceProviderTokenGenerator {

    public function generateToken(): string {
        return hash('sha512', bin2hex(openssl_random_pseudo_bytes(128)));
    }
}