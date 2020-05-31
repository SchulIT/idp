<?php

namespace App\Security;

use Anyx\LoginGateBundle\Service\UsernameResolverInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UsernameResolver implements UsernameResolverInterface {

    private $utils;

    public function __construct(AuthenticationUtils $utils) {
        $this->utils = $utils;
    }

    /**
     * @inheritDoc
     */
    public function resolve(Request $request) {
        return $this->utils->getLastUsername();
    }
}