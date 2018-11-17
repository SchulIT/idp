<?php

namespace App\Security\Authentication\Token;

use App\Entity\Application;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class ApplicationToken extends AbstractToken {

    private $application;
    private $apiKey;

    public function __construct($apiKey, Application $application = null, array $roles = []) {
        parent::__construct($roles);

        $this->apiKey = $apiKey;
        $this->application = $application;

        if($application === null) {
            $this->setAuthenticated(false);
        }
    }

    /**
     * @inheritDoc
     */
    public function getCredentials() {
        return $this->apiKey;
    }
}