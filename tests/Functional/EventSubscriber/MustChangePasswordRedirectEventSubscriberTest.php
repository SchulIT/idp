<?php

namespace App\Tests\Functional\EventSubscriber;

use App\Entity\User;
use App\Entity\UserType;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MustChangePasswordRedirectEventSubscriberTest extends WebTestCase {

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

    public function testMustNotChangePassword() {
        $this->user->setMustChangePassword(false);
        $this->em->persist($this->user);
        $this->em->flush();

        $this->client->restart();

        $this->client->followRedirects(true);

        $crawler = $this->client->request('GET', '/login');
        $button = $crawler->filter('button[type=submit]')->first();
        $form = $button->form();

        $form['_username']->setValue('testuser');
        $form['_password']->setValue('Test1234$');

        $crawler = $this->client->submit($form);
        $this->assertEquals('http://localhost/dashboard', $crawler->getUri(), 'Tests whether we land on the dashboard after successful login');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), 'Ensure that we have a HTTP 200 at the dashboard');
    }

    public function testMustChangePassword() {
        $this->user->setMustChangePassword(true);
        $this->em->persist($this->user);
        $this->em->flush();

        $this->client->restart();

        $this->client->followRedirects(true);

        $crawler = $this->client->request('GET', '/login');
        $button = $crawler->filter('button[type=submit]')->first();
        $form = $button->form();

        $form['_username']->setValue('testuser');
        $form['_password']->setValue('Test1234$');

        $crawler = $this->client->submit($form);
        $this->assertEquals('http://localhost/profile/password', $crawler->getUri(), 'Tests whether we land on the password change page after successful login');

        $button = $crawler->filter('button[type=submit]')->first();
        $form = $button->form();
        $form['password_change[currentPassword]']->setValue('Test1234$');
        $form['password_change[newPassword][first]']->setValue('Test23456$');
        $form['password_change[newPassword][second]']->setValue('Test23456$');

        $crawler = $this->client->submit($form);
        $crawler = $this->client->request('GET', '/dashboard');
        $this->assertEquals('http://localhost/dashboard', $crawler->getUri(), 'Tests whether we land on the dashboard after successful password change');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), 'Ensure that we have a HTTP 200 at the dashboard');
    }

    public function testMustChangePasswordPlusIncomingSamlRequest() {
        $this->user->setMustChangePassword(true);
        $this->em->persist($this->user);
        $this->em->flush();

        $this->client->restart();

        $this->client->followRedirects(true);
        $this->client->setMaxRedirects(10);

        $crawler = $this->client->request('POST', '/idp/saml', [
            'SAMLRequest' => 'testrequest'
        ]);
        $button = $crawler->filter('button[type=submit]')->first();
        $form = $button->form();

        $form['_username']->setValue('testuser');
        $form['_password']->setValue('Test1234$');

        $crawler = $this->client->submit($form);
        $this->assertEquals('http://localhost/profile/password', $crawler->getUri(), 'Tests whether we land on the password change page after successful login');
    }
}