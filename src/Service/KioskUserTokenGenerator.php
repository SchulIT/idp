<?php

namespace App\Service;

use App\Repository\KioskUserRepositoryInterface;

class KioskUserTokenGenerator {

    private $repository;

    public function __construct(KioskUserRepositoryInterface $repository) {
        $this->repository = $repository;
    }

    /**
     * Generates an unused token.
     *
     * @return string
     */
    public function generateToken() {
        do {
            $token = bin2hex(openssl_random_pseudo_bytes(32));
            $user = $this->repository
                ->findOneByToken($token);
        } while($user !== null);

        return $token;
    }
}