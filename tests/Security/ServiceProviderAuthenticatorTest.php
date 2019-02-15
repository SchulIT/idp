<?php

namespace App\Tests\Security;

use App\Entity\ServiceProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ServiceProviderAuthenticatorTest extends WebTestCase {

    /** @var EntityManagerInterface */
    private $em;

    /** @var Client */
    private $client;

    /** @var ServiceProvider */
    private $serviceProvider;

    public function setUp() {
        $this->client = static::createClient();

        $this->em = $this->client->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->serviceProvider = (new ServiceProvider())
            ->setName('Test SP')
            ->setDescription('Test SP')
            ->setToken('test-sp-token')
            ->setEntityId('https://sp.school.it/')
            ->setAcs('https://sp.school.it/acs')
            ->setCertificate('')
            ->setUrl('https://sp.schoo.it/');

        $this->em->persist($this->serviceProvider);
        $this->em->flush();
    }

    public function tearDown() {
        $this->em->remove($this->serviceProvider);
        $this->em->flush();

        $this->em->close();
        $this->em = null;

        parent::tearDown();
    }

    public function testAuthenticateNoToken() {
        $this->client->request('GET', '/exchange');

        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('application/json', $this->client->getResponse()->headers->get('Content-Type'));
    }

    public function testAuthenticateValidToken() {
        $this->client->request('GET', '/exchange', [], [], [
            'HTTP_X_TOKEN' => 'test-sp-token'
        ]);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('application/json', $this->client->getResponse()->headers->get('Content-Type'));
    }

    public function testAuthenticateInvalidToken() {
        $this->client->request('GET', '/exchange', [], [], [
            'HTTP_X_TOKEN' => 'invalid-sp-token'
        ]);

        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('application/json', $this->client->getResponse()->headers->get('Content-Type'));
    }
}