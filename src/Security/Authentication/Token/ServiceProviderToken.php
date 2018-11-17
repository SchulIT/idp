<?php

namespace App\Security\Authentication\Token;

use App\Entity\ServiceProvider;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class ServiceProviderToken extends AbstractToken {

    private $serviceProvider;
    private $token;

    public function __construct($token, ServiceProvider $serviceProvider = null) {
        parent::__construct(['ROLE_SERVICEPROVIDER']);

        $this->token = $token;
        $this->serviceProvider = $serviceProvider;

        $this->setAuthenticated($serviceProvider !== null);
    }

    /**
     * @return ServiceProvider|null
     */
    public function getServiceProvider(): ?ServiceProvider {
        return $this->serviceProvider;
    }

    /**
     * @inheritDoc
     */
    public function getCredentials() {
        return $this->token;
    }
}