<?php

namespace App\Tests\Functional\EventSubscriber;

use App\Entity\User;
use App\Entity\UserType;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HandleSamlRequestSubscriberTest extends WebTestCase {

    private ?ObjectManager $em;
    private KernelBrowser $client;
    private ?User $user;
    private ?UserType $userType;

    public function setUp(): void {
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
            ->setUsername('testuser')
            ->setFirstname('Test')
            ->setLastname('User')
            ->setIsActive(true)
            ->setEmail('testuser@school.it')
            ->setPassword('$2y$12$tMvjq5Pa1c35RDuvquVwh.35jCqBivwgHfRnHTomatFMKE2HAMMdG'); // Test1234$

        $this->em->persist($this->user);
        $this->em->flush();
    }

    public function tearDown(): void {
        $this->em->close();
        $this->em = $this->user = null;

        parent::tearDown();
    }

    public function testStoreAndRedirectSamlPostRequest() {
        $this->client->followRedirects(true);
        $crawler = $this->client->request('POST', '/idp/saml', [
            'SAMLRequest' => 'foo'
        ]);

        $this->assertEquals('http://localhost/login', $crawler->getUri(), 'Ensure we were redirected to the login page.');

        $button = $crawler->filter('button[type=submit]')->first();
        $form = $button->form();

        $form['_username']->setValue('testuser');
        $form['_password']->setValue('Test1234$');

        $crawler = $this->client->submit($form);

        $this->assertEquals('http://localhost/idp/saml', $crawler->getUri(), 'Ensure we were redirected to the SAML SSO page after login.');
    }
}