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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\SimpleFormAuthenticatorInterface;

class UserAuthenticator implements SimpleFormAuthenticatorInterface {

    private $isActiveDirectoryEnabled;
    private $encoder;
    private $logger;
    private $adAuth;
    private $userCreator;
    private $userRepository;

    public function __construct($isActiveDirectoryEnabled, UserPasswordEncoderInterface $encoder, UserRepositoryInterface $userRepository, AdAuthInterface $adAuth, UserCreator $userCreator, LoggerInterface $logger = null) {
        $this->isActiveDirectoryEnabled = $isActiveDirectoryEnabled;
        $this->encoder = $encoder;
        $this->userRepository = $userRepository;
        $this->adAuth = $adAuth;
        $this->userCreator = $userCreator;
        $this->logger = $logger ?? new NullLogger();
    }

    public function createToken(Request $request, $username, $password, $providerKey) {
        return new UsernamePasswordToken($username, $password, $providerKey);
    }

    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey) {
        $user = null;

        try {
            $user = $userProvider->loadUserByUsername($token->getUsername());

            if($user instanceof ActiveDirectoryUser) {
                $user = $this->authenticateUsingActiveDirectory($token, $user);
            }
        } catch(UsernameNotFoundException $e) {
            $user = $this->authenticateUsingActiveDirectory($token);
        }

        if($user !== null && $this->encoder->isPasswordValid($user, $token->getCredentials())) {
            return new UsernamePasswordToken($user, $user->getPassword(), $providerKey, $user->getRoles());
        }

        throw new CustomUserMessageAuthenticationException('invalid_credentials');
    }

    protected function authenticateUsingActiveDirectory(TokenInterface $token, ActiveDirectoryUser $adUser = null) {
        if($this->isActiveDirectoryEnabled !== true) {
            return $adUser;
        }

        try {
            $credentials = new Credentials($token->getUsername(), $token->getCredentials());
            /** @var AuthenticationResponse $response */
            $response = $this->adAuth->authenticate($credentials);

            if($response->isSuccess() !== true) {
                $this->logger
                    ->notice(sprintf('Failed to authenticate "%s" using Active Directory', $token->getUsername()));

                // password not valid
                return null;
            }

            if($this->userCreator->canCreateUser($response)) {
                $user = $this->userCreator->createUser($response, $adUser);

                $user->setPassword($this->encoder->encodePassword($user, $token->getCredentials()));

                $this->userRepository->persist($user);

                return $user;
            } else {
                $this->logger
                    ->notice(sprintf('User "%s" tried to authenticate but this user cannot be created from active directory', $token->getUsername()));

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

    public function supportsToken(TokenInterface $token, $providerKey) {
        return $token instanceof UsernamePasswordToken
            && $token->getProviderKey() === $providerKey;
    }
}