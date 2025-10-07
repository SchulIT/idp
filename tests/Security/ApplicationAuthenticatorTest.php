<?php

declare(strict_types=1);

namespace App\Tests\Security;

use App\Entity\Application;
use App\Entity\ApplicationScope;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ApplicationAuthenticatorTest extends WebTestCase {

    private EntityManagerInterface|null $em;

    private KernelBrowser|null $client;

    private Application|null $application;

    public function setUp(): void {
        $this->client = self::createClient();

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

    #[\Override]
    public function tearDown(): void {
        $this->em->remove($this->application);
        $this->em->flush();

        $this->em->close();
        $this->em = null;

        parent::tearDown();
    }

    public function testAuthenticateNoApiKey(): void {
        $this->client->request(Request::METHOD_GET, '/api/user_types', [], [], [
            'HTTP_ACCEPT' => 'application/json'
        ]);

        $this->client->getResponse();

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('application/json', $this->client->getResponse()->headers->get('Content-Type'));
    }

    public function testAuthenticateValidApplication(): void {
        $this->client->request(Request::METHOD_GET, '/api/user_types', [], [], [
            'HTTP_X_TOKEN' => 'api-key',
            'Accept' => 'application/json'
        ]);

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('application/json', $this->client->getResponse()->headers->get('Content-Type'));
    }

    public function testAuthenticateInvalidApplication(): void {
        $this->client->request(Request::METHOD_GET, '/api/user_types', [], [], [
            'HTTP_X_TOKEN' => 'invalid-api-key'
        ]);

        $this->assertEquals(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('application/json', $this->client->getResponse()->headers->get('Content-Type'));
    }
}
