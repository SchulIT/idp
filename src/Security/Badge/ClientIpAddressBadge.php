<?php

declare(strict_types=1);

namespace App\Security\Badge;

class ClientIpAddressBadge extends AbstractBadge {

    public function __construct(private readonly array $validIpAddresses)
    {
    }

    public function getValidIpAddresses(): array {
        return $this->validIpAddresses;
    }
}
