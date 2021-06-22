<?php

namespace App\Security\Badge;

class ClientIpAddressBadge extends AbstractBadge {

    private $validIpAddresses;

    public function __construct(array $validIpAddresses) {
        $this->validIpAddresses = $validIpAddresses;
    }

    public function getValidIpAddresses(): array {
        return $this->validIpAddresses;
    }
}