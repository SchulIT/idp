<?php

declare(strict_types=1);

namespace App\Tests\Functional\Authentication;

use App\Entity\KioskUser;
use App\Entity\User;
use App\Entity\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class KioskLoginTest extends WebTestCase {
    private EntityManagerInterface|null $em;

    private KernelBrowser|null $client;

    private ?User $user = null;

    private ?UserType $userType = null;

    private ?KioskUser $kiosk = null;

    public function setUp(): void {
        self::ensureKernelShutdown();
        $this->client = self::createClient();

        $this->em = $this->client->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->userType = (new UserType())
            ->setName('Member')
            ->setAlias('member')
            ->setEduPerson(['member']);

        $this->em->persist($this->userType);
        $this->em->flush();

        $this->user = (new User())
            ->setType($this->userType)
            ->setUsername('kiosk@example.com')
            ->setPassword('$2y$12$tMvjq5Pa1c35RDuvquVwh.35jCqBivwgHfRnHTomatFMKE2HAMMdG'); // Test1234$

        $this->em->persist($this->user);
        $this->em->flush();

        $this->kiosk = (new KioskUser())
            ->setUser($this->user)
            ->setToken('abc')
            ->setIpAddresses('127.0.0.1,::1');
        $this->em->persist($this->kiosk);
        $this->em->flush();
    }

    #[\Override]
    public function tearDown(): void {
        $this->em->close();
        $this->em = $this->user = $this->kiosk = $this->userType = null;

        parent::tearDown();
    }

    public function testLoginValidToken(): void {
        $this->client->restart();
        $this->client->followRedirects(true);

        $this->kiosk->setIpAddresses('127.0.0.1,::1');
        $this->em->persist($this->kiosk);
        $this->em->flush();

        $crawler = $this->client->request(Request::METHOD_GET, '/login/check?token=abc');
        $this->assertEquals('http://localhost/dashboard', $crawler->getUri(), 'Tests whether we land at the dashboard page');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), 'Ensure that we have a HTTP 200 at the login page');

        $tokenStorage = $this->client->getContainer()->get('security.token_storage');

        $this->assertNotNull($tokenStorage->getToken());

        $token = $tokenStorage->getToken();
        $this->assertEquals($this->user->getUserIdentifier(), $token->getUserIdentifier());
    }

    public function testLoginValidTokenInvalidIpAddress(): void {
        $this->client->restart();
        $this->client->followRedirects(true);

        $this->kiosk->setIpAddresses('10.0.0.1');
        $this->em->persist($this->kiosk);
        $this->em->flush();

        $crawler = $this->client->request(Request::METHOD_GET, '/login/check?token=abc');
        $this->assertEquals('http://localhost/login', $crawler->getUri(), 'Tests whether we land at the login page');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), 'Ensure that we have a HTTP 200 at the login page');
    }

    public function testInvalidLogin(): void {
        $this->client->restart();
        $this->client->followRedirects(true);

        $crawler = $this->client->request(Request::METHOD_GET, '/login/check?token=foo');
        $this->assertEquals('http://localhost/login', $crawler->getUri(), 'Tests whether we land at the login page');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), 'Ensure that we have a HTTP 200 at the login page');
    }
}
