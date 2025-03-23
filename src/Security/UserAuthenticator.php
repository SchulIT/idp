<?php

namespace App\Security;

use AdAuth\AdAuthInterface;
use AdAuth\Credentials;
use AdAuth\Response\AuthenticationResponse;
use AdAuth\Response\AuthenticationSuccessResponse;
use AdAuth\SocketException;
use App\Entity\ActiveDirectoryUser;
use App\Repository\UserRepositoryInterface;
use App\Security\Badge\CachePasswordBadge;
use App\Settings\LoginSettings;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class UserAuthenticator extends AbstractLoginFormAuthenticator {

    use TargetPathTrait;

    public function __construct(private readonly bool $isActiveDirectoryEnabled,
                                private readonly string $loginRoute,
                                private readonly string $checkRoute,
                                private readonly UserPasswordHasherInterface $hasher,
                                private readonly AdAuthInterface $adAuth,
                                private readonly RouterInterface $router,
                                private readonly LoginSettings $loginSettings,
                                private readonly UserRepositoryInterface $userRepository,
                                private readonly LoggerInterface $logger) {
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
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $firewallName): ?Response {
        if($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse('/');
    }

    private function authenticateUsingActiveDirectory(Credentials $credentials, ActiveDirectoryUser $user): bool {
        try {
            $response = $this->adAuth->authenticate($credentials);

            if(!$response instanceof AuthenticationSuccessResponse) {
                // password not valid
                $this->logger
                    ->notice(sprintf('Failed to authenticate "%s" using Active Directory', $credentials->getUsername()));

                return false;
            }

            return true;
        } catch (SocketException) {
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
    public function authenticate(Request $request): Passport {
        $username = $request->request->get('_username');
        $password = $request->request->get('_password');
        $csrfToken = $request->request->get('_csrf_token');

        if(empty($username)) {
            throw new BadRequestHttpException('Username must not be empty');
        }

        if(empty($password)) {
            throw new BadRequestHttpException('Password must not empty');
        }

        if($this->loginSettings->allowEmailOnLogin && $this->userRepository->findOneByUsername($username) === null) {
            $user = $this->userRepository->findOneByEmail($username);
            if($user !== null && !empty($user->getEmail())) {
                $username = $user->getUsername();
            }
        }

        return new Passport(
            new UserBadge($username),
            new CustomCredentials(
                function($password, PasswordAuthenticatedUserInterface $user) {
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