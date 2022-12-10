<?php

namespace App\Message;

class MustProvisionUser {
    public function __construct(private int $userId)
    {
    }

    public function getUserId(): int {
        return $this->userId;
    }
}