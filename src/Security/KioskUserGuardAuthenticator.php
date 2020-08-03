<?php

namespace App\Security;

use App\Repository\KioskUserRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;

class KioskUserGuardAuthenticator extends AbstractFormLoginAuthenticator {

    private $repository;
    private $urlGenerator;

    public function __construct(KioskUserRepositoryInterface $repository, UrlGeneratorInterface $urlGenerator) {
        $this->repository = $repository;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @inheritDoc
     */
    public function supports(Request $request) {
        return $request->query->has('token');
    }

    /**
     * @inheritDoc
     */
    public function getCredentials(Request $request) {
        $request->request->set('_remember_me', 'true'); // Force remember me

        return $request->query->get('token');
    }

    /**
     * @inheritDoc
     */
    public function getUser($credentials, UserProviderInterface $userProvider) {
        $user = $this->repository->findOneByToken($credentials);

        if($user === null) {
            return null;
        }

        return $user->getUser();
    }

    /**
     * @inheritDoc
     */
    public function checkCredentials($credentials, UserInterface $user) {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey) {
        return new RedirectResponse($this->urlGenerator->generate('dashboard'));
    }

    /**
     * @inheritDoc
     */
    public function supportsRememberMe() {
        return true;
    }

    /**
     * @inheritDoc
     */
    protected function getLoginUrl() {
        return $this->urlGenerator->generate('login');
    }
}