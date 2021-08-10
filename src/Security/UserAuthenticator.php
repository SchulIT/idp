<?php

namespace App\Security;

use AdAuth\AdAuthInterface;
use AdAuth\Credentials;
use AdAuth\Response\AuthenticationResponse;
use AdAuth\SocketException;
use App\Entity\ActiveDirectoryUser;
use App\Repository\UserRepositoryInterface;
use App\Security\Badge\CachePasswordBadge;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class UserAuthenticator extends AbstractLoginFormAuthenticator {

    use TargetPathTrait;

    private $isActiveDirectoryEnabled;
    private $hasher;
    private $logger;
    private $adAuth;
    private $userRepository;

    private $loginRoute;
    private $checkRoute;
    private $router;

    public function __construct($isActiveDirectoryEnabled, $loginRoute, $checkRoute, UserPasswordHasherInterface $hasher, UserRepositoryInterface $userRepository,
                                AdAuthInterface $adAuth, RouterInterface $router, LoggerInterface $logger = null) {
        $this->isActiveDirectoryEnabled = $isActiveDirectoryEnabled;
        $this->hasher = $hasher;
        $this->userRepository = $userRepository;
        $this->adAuth = $adAuth;
        $this->loginRoute = $loginRoute;
        $this->checkRoute = $checkRoute;
        $this->router = $router;

        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @inheritDoc
     */
    protected function getLoginUrl(Request $request): string {
        return $this->router->generate($this->loginRoute);
    }

    /**
     * @inheritDoc
     */
    public function supports(Request $request): bool {
        return $request->attributes->get('_route') === $this->checkRoute
            && $request->isMethod('POST');
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): ?Response {
        if($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse('/');
    }

    private function authenticateUsingActiveDirectory(Credentials $credentials, ActiveDirectoryUser $user): bool {
        try {
            /** @var AuthenticationResponse $response */
            $response = $this->adAuth->authenticate($credentials);

            if($response->isSuccess() !== true) {
                // password not valid
                $this->logger
                    ->notice(sprintf('Failed to authenticate "%s" using Active Directory', $credentials->getUsername()));

                return false;
            }

            return true;
        } catch (SocketException $socketException) {
            // Fallback
            if($this->hasher->isPasswordValid($user, $credentials->getPassword())) {
                return true;
            }

            throw new CustomUserMessageAuthenticationException('server_unavailable');
        }
    }

    /**
     * @inheritDoc
     */
    public function authenticate(Request $request): PassportInterface {
        $username = $request->request->get('_username');
        $password = $request->request->get('_password');
        $csrfToken = $request->request->get('_csrf_token');

        return new Passport(
            new UserBadge($username),
            new CustomCredentials(
                function($password, UserInterface $user) {
                    if($user instanceof ActiveDirectoryUser && $this->isActiveDirectoryEnabled === true) {
                        return $this->authenticateUsingActiveDirectory(new Credentials($user->getUserIdentifier(), $password), $user);
                    } else {
                        return $this->hasher->isPasswordValid($user, $password);
                    }
                },
                $password
            ),
            [
                new CsrfTokenBadge('authenticate', $csrfToken),
                new RememberMeBadge(),
                new CachePasswordBadge($password, $this->hasher)
            ]
        );
    }
}