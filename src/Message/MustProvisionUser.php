<?php

declare(strict_types=1);

namespace App\Message;

class MustProvisionUser {
    public function __construct(private readonly int $userId)
    {
    }

    public function getUserId(): int {
        return $this->userId;
    }
}
