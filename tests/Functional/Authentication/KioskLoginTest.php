<?php

namespace App\Tests\Functional\Authentication;

use App\Entity\KioskUser;
use App\Entity\User;
use App\Entity\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class KioskLoginTest extends WebTestCase {
    /** @var EntityManagerInterface */
    private $em;

    /** @var Client */
    private $client;

    /** @var User */
    private $user;

    /** @var UserType */
    private $userType;

    /** @var KioskUser */
    private $kiosk;

    public function setUp() {
        $this->client = static::createClient();

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

    public function tearDown(): void {
        $this->em->close();
        $this->em = $this->user = $this->kiosk = $this->userType = null;

        parent::tearDown();
    }

    public function testLoginValidToken() {
        $this->client->restart();
        $this->client->followRedirects(true);

        $this->kiosk->setIpAddresses('127.0.0.1,::1');
        $this->em->persist($this->kiosk);
        $this->em->flush();

        $crawler = $this->client->request('GET', '/login/check?token=abc');
        var_dump($this->client->getResponse()->getStatusCode());
        $this->assertEquals('http://localhost/dashboard', $crawler->getUri(), 'Tests whether we land at the dashboard page');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), 'Ensure that we have a HTTP 200 at the login page');

        $tokenStorage = $this->client->getContainer()->get('security.token_storage');

        $this->assertNotNull($tokenStorage->getToken());

        $token = $tokenStorage->getToken();
        $this->assertEquals($this->user->getUsername(), $token->getUsername());
    }

    public function testLoginValidTokenInvalidIpAddress() {
        $this->client->restart();
        $this->client->followRedirects(true);

        $this->kiosk->setIpAddresses('10.0.0.1');
        $this->em->persist($this->kiosk);
        $this->em->flush();

        $crawler = $this->client->request('GET', '/login/check?token=abc');
        $this->assertEquals('http://localhost/login', $crawler->getUri(), 'Tests whether we land at the login page');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), 'Ensure that we have a HTTP 200 at the login page');
    }

    public function testInvalidLogin() {
        $this->client->restart();
        $this->client->followRedirects(true);

        $crawler = $this->client->request('GET', '/login/check?token=foo');
        $this->assertEquals('http://localhost/login', $crawler->getUri(), 'Tests whether we land at the login page');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), 'Ensure that we have a HTTP 200 at the login page');
    }
}