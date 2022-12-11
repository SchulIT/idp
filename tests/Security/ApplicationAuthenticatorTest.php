<?php

namespace App\Tests\Security;

use App\Entity\Application;
use App\Entity\ApplicationScope;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApplicationAuthenticatorTest extends WebTestCase {

    /** @var EntityManagerInterface */
    private $em;

    /** @var KernelBrowser */
    private $client;

    /** @var Application */
    private $application;

    public function setUp(): void {
        $this->client = static::createClient();

        $this->em = $this->client->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->application = (new Application())
            ->setName('Test app')
            ->setDescription('Just a test app')
            ->setLastActivity(new \DateTime())
            ->setApiKey('api-key')
            ->setScope(ApplicationScope::Api);

        $this->em->persist($this->application);
        $this->em->flush();
    }

    public function tearDown(): void {
        $this->em->remove($this->application);
        $this->em->flush();

        $this->em->close();
        $this->em = null;

        parent::tearDown();
    }

    public function testAuthenticateNoApiKey() {
        $this->client->request('GET', '/api', [], [], [
            'HTTP_ACCEPT' => 'application/json'
        ]);

        $response = $this->client->getResponse();

        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('application/json', $this->client->getResponse()->headers->get('Content-Type'));
    }

    public function testAuthenticateValidApplication() {
        $this->client->request('GET', '/api', [], [], [
            'HTTP_X_TOKEN' => 'api-key',
            'Accept' => 'application/json'
        ]);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('application/json', $this->client->getResponse()->headers->get('Content-Type'));
    }

    public function testAuthenticateInvalidApplication() {
        $this->client->request('GET', '/api', [], [], [
            'HTTP_X_TOKEN' => 'invalid-api-key'
        ]);

        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('application/json', $this->client->getResponse()->headers->get('Content-Type'));
    }
}