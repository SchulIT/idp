<?php

namespace App\Tests\Listener;

use App\Listener\HandleSamlRequestListener;
use PHPUnit\Framework\TestCase;
use Scheb\TwoFactorBundle\Security\Authentication\Token\TwoFactorToken;
use SchoolIT\LightSamlIdpBundle\RequestStorage\RequestStorageInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface;

class HandleSamlRequestListenerTest extends TestCase {
    public function testNoRedirectNoMasterRequest() {
        $request = Request::create('/');

        $kernel = $this->createMock(HttpKernelInterface::class);

        $event = new RequestEvent($kernel, $request, HttpKernelInterface::SUB_REQUEST);

        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $requestStorage = $this->createMock(RequestStorageInterface::class);
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $listener = new HandleSamlRequestListener($tokenStorage, $requestStorage, $urlGenerator);
        $listener->onKernelRequest($event);

        $this->assertNull($event->getResponse());
    }

    public function testNoRedirectNoSamlRequest() {
        $request = Request::create('/');

        $kernel = $this->createMock(HttpKernelInterface::class);

        $event = new RequestEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST);

        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage->method('getToken')
            ->willReturn($this->getToken());

        $requestStorage = $this->createMock(RequestStorageInterface::class);
        $requestStorage->method('has')
            ->willReturn(false);
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);

        $listener = new HandleSamlRequestListener($tokenStorage, $requestStorage, $urlGenerator);
        $listener->onKernelRequest($event);

        $this->assertNull($event->getResponse());
    }

    public function testRedirectExistingSamlRequest() {
        $request = Request::create('/');

        $kernel = $this->createMock(HttpKernelInterface::class);

        $event = new RequestEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST);

        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage->method('getToken')
            ->willReturn($this->getToken());

        $requestStorage = $this->createMock(RequestStorageInterface::class);
        $requestStorage->method('has')
            ->willReturn(true);
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->method('generate')
            ->willReturn('/saml/sso');

        $listener = new HandleSamlRequestListener($tokenStorage, $requestStorage, $urlGenerator);
        $listener->onKernelRequest($event);

        $response = $event->getResponse();

        $this->assertNotNull($response);
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals('/saml/sso', $response->headers->get('Location'));
    }

    public function testNoRedirectExistingSamlRequestNotFullyAuthorized() {
        $request = Request::create('/');

        $kernel = $this->createMock(HttpKernelInterface::class);

        $event = new RequestEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST);

        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage->method('getToken')
            ->willReturn($this->createMock(TwoFactorToken::class));

        $requestStorage = $this->createMock(RequestStorageInterface::class);
        $requestStorage->method('has')
            ->willReturn(true);
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->method('generate')
            ->willReturn('/saml/sso');

        $listener = new HandleSamlRequestListener($tokenStorage, $requestStorage, $urlGenerator);
        $listener->onKernelRequest($event);

        $this->assertNull($event->getResponse());
    }

    public function testNoRedirectExistingSamlRequestAnonymousToken() {
        $request = Request::create('/');

        $kernel = $this->createMock(HttpKernelInterface::class);

        $event = new RequestEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST);

        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage->method('getToken')
            ->willReturn($this->createMock(AnonymousToken::class));

        $requestStorage = $this->createMock(RequestStorageInterface::class);
        $requestStorage->method('has')
            ->willReturn(true);
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->method('generate')
            ->willReturn('/saml/sso');

        $listener = new HandleSamlRequestListener($tokenStorage, $requestStorage, $urlGenerator);
        $listener->onKernelRequest($event);

        $this->assertNull($event->getResponse());
    }

    public function testNoRedirectExistingSamlRequestOnSsoPage() {
        $request = Request::create('/saml/sso');
        $request->attributes->set('_route', 'idp_saml');

        $kernel = $this->createMock(HttpKernelInterface::class);

        $event = new RequestEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST);

        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage->method('getToken')
            ->willReturn($this->getToken());

        $requestStorage = $this->createMock(RequestStorageInterface::class);
        $requestStorage->method('has')
            ->willReturn(true);
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->method('generate')
            ->willReturn('/saml/sso');

        $listener = new HandleSamlRequestListener($tokenStorage, $requestStorage, $urlGenerator);
        $listener->onKernelRequest($event);

        $this->assertNull($event->getResponse());
    }

    private function getToken() {
        $user = $this->createMock(UserInterface::class);
        return new UsernamePasswordToken($user, 'password', 'test', ['ROLE_USER']);
    }
}