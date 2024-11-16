<?php

namespace App\Service;

use App\Repository\KioskUserRepositoryInterface;

class KioskUserTokenGenerator {

    public function __construct(private readonly KioskUserRepositoryInterface $repository)
    {
    }

    /**
     * Generates an unused token.
     */
    public function generateToken(): string {
        do {
            $token = bin2hex(openssl_random_pseudo_bytes(32));
            $user = $this->repository
                ->findOneByToken($token);
        } while($user !== null);

        return $token;
    }
}