<?php

namespace App\Security;

use AdAuth\AdAuthInterface;
use AdAuth\Credentials;
use AdAuth\Response\AuthenticationResponse;
use AdAuth\SocketException;
use App\Entity\ActiveDirectoryUser;
use App\Repository\UserRepositoryInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class UserAuthenticator extends AbstractFormLoginAuthenticator {

    use TargetPathTrait;

    private $isActiveDirectoryEnabled;
    private $encoder;
    private $logger;
    private $adAuth;
    private $userCreator;
    private $userRepository;

    private $loginRoute;
    private $checkRoute;
    private $router;
    private $csrfTokenManager;

    public function __construct($isActiveDirectoryEnabled, $loginRoute, $checkRoute, UserPasswordEncoderInterface $encoder, UserRepositoryInterface $userRepository,
                                AdAuthInterface $adAuth, UserCreator $userCreator, RouterInterface $router, CsrfTokenManagerInterface $csrfTokenManager,
                                LoggerInterface $logger = null) {
        $this->isActiveDirectoryEnabled = $isActiveDirectoryEnabled;
        $this->encoder = $encoder;
        $this->userRepository = $userRepository;
        $this->adAuth = $adAuth;
        $this->userCreator = $userCreator;
        $this->loginRoute = $loginRoute;
        $this->checkRoute = $checkRoute;
        $this->router = $router;
        $this->csrfTokenManager = $csrfTokenManager;

        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @inheritDoc
     */
    protected function getLoginUrl() {
        return $this->router->generate($this->loginRoute);
    }

    /**
     * @inheritDoc
     */
    public function supports(Request $request) {
        return $request->attributes->get('_route') === $this->checkRoute
            && $request->isMethod('POST');
    }

    /**
     * @inheritDoc
     */
    public function getCredentials(Request $request) {
        $credentials = [
            'username' => $request->request->get('_username'),
            'password' => $request->request->get('_password'),
            'csrf_token' => $request->request->get('_csrf_token')
        ];

        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['username']
        );

        return $credentials;
    }

    /**
     * @inheritDoc
     */
    public function getUser($credentials, UserProviderInterface $userProvider) {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);

        if(!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $user = null;

        try {
            $user = $userProvider->loadUserByUsername($credentials['username']);

            if($user instanceof ActiveDirectoryUser) {
                $user = $this->authenticateUsingActiveDirectory(new Credentials($credentials['username'], $credentials['password']), $user);
            }
        } catch(UsernameNotFoundException $e) {
            $user = $this->authenticateUsingActiveDirectory(new Credentials($credentials['username'], $credentials['password']));
        }

        if(!$user === null) {
            throw new CustomUserMessageAuthenticationException('invalid_credentials');
        }

        return $user;
    }

    /**
     * @inheritDoc
     */
    public function checkCredentials($credentials, UserInterface $user) {
        return $this->encoder->isPasswordValid($user, $credentials['password']);
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey) {
        if($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse('/');
    }

    protected function authenticateUsingActiveDirectory(Credentials $credentials, ActiveDirectoryUser $adUser = null) {
        if($this->isActiveDirectoryEnabled !== true) {
            return $adUser;
        }

        try {
            /** @var AuthenticationResponse $response */
            $response = $this->adAuth->authenticate($credentials);

            if($response->isSuccess() !== true) {
                $this->logger
                    ->notice(sprintf('Failed to authenticate "%s" using Active Directory', $credentials->getUsername()));

                // password not valid
                return null;
            }

            if($this->userCreator->canCreateUser($response)) {
                $user = $this->userCreator->createUser($response, $adUser);

                $user->setPassword($this->encoder->encodePassword($user, $credentials->getPassword()));

                $this->userRepository->persist($user);

                return $user;
            } else {
                $this->logger
                    ->notice(sprintf('User "%s" tried to authenticate but this user cannot be created from active directory', $credentials->getUsername()));

                throw new CustomUserMessageAuthenticationException('not_allowed');
            }
        } catch(SocketException $e) {
            $this->logger
                ->critical('Authentication server is not available');

            throw new CustomUserMessageAuthenticationException('server_unavailable');
        } catch(\Exception $e) {
            $this->logger->critical(
                sprintf('Authentication failed', [
                    'exception' => $e
                ])
            );

            throw new CustomUserMessageAuthenticationException('unknown_error');
        }
    }

    /*
     * It is important to implement this method because (as of now, Dec 2018), the TwoFactorBundle
     * only works with UsernamePasswordToken
     */
    public function createAuthenticatedToken(UserInterface $user, $providerKey) {
        return new UsernamePasswordToken($user, null, $providerKey, $user->getRoles());
    }

}