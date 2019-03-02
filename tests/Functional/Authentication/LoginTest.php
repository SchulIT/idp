<?php

namespace App\Tests\Functional\Authentication;

use App\Entity\User;
use App\Entity\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Sonata\GoogleAuthenticator\GoogleAuthenticator;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Role\Role;

class LoginTest extends WebTestCase {

    /** @var EntityManagerInterface */
    private $em;

    /** @var Client */
    private $client;

    /** @var User */
    private $user;

    /** @var User */
    private $twoFactorUser;

    /** @var UserType */
    private $userType;

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
            ->setUsername('testuser')
            ->setFirstname('Test')
            ->setLastname('User')
            ->setIsActive(true)
            ->setEmail('testuser@school.it')
            ->setPassword('$2y$12$tMvjq5Pa1c35RDuvquVwh.35jCqBivwgHfRnHTomatFMKE2HAMMdG'); // Test1234$

        $this->em->persist($this->user);
        $this->em->flush();

        $this->twoFactorUser = (new User())
            ->setType($this->userType)
            ->setUsername('testuser2')
            ->setFirstname('Test')
            ->setLastname('User')
            ->setIsActive(true)
            ->setEmail('testuser2@school.it')
            ->setPassword('$2y$12$tMvjq5Pa1c35RDuvquVwh.35jCqBivwgHfRnHTomatFMKE2HAMMdG'); // Test1234$
        $this->twoFactorUser
            ->setGoogleAuthenticatorSecret('ABCDEFGHIJ');

        $this->em->persist($this->twoFactorUser);
        $this->em->flush();
    }

    public function tearDown() {
        $this->em->close();
        $this->em = $this->user = $this->twoFactorUser = $this->userType = null;

        parent::tearDown();
    }

    public function testLoginNoTwoFactor() {
        $this->client->restart();

        $this->client->followRedirects(true);

        $crawler = $this->client->request('GET', '/dashboard');
        $this->assertEquals('http://localhost/login', $crawler->getUri(), 'Tests whether we land at the login page');

        $button = $crawler->filter('button[type=submit]')->first();
        $form = $button->form();

        $form['_username']->setValue('testuser');
        $form['_password']->setValue('Test1234$');

        $crawler = $this->client->submit($form);
        $this->assertEquals('http://localhost/dashboard', $crawler->getUri(), 'Tests whether we land on the dashboard after successful login');

        $tokenStorage = $this->client->getContainer()->get('security.token_storage');

        $this->assertNotNull($tokenStorage->getToken());

        $token = $tokenStorage->getToken();

        $this->assertEquals($this->user->getUsername(), $token->getUsername());
        $this->assertEquals($this->user->getRoles(), $this->getRoles($token->getRoles()));
    }

    public function testLoginTwoFactor() {
        $this->client->restart();

        $this->client->followRedirects(true);

        $crawler = $this->client->request('GET', '/dashboard');
        $this->assertEquals('http://localhost/login', $crawler->getUri(), 'Tests whether we land at the login page');

        $button = $crawler->filter('button[type=submit]')->first();
        $form = $button->form();

        $form['_username']->setValue('testuser2');
        $form['_password']->setValue('Test1234$');

        $crawler = $this->client->submit($form);
        $this->assertEquals('http://localhost/login/two_factor', $crawler->getUri(), 'Tests whether we land on the two factor page');

        $tokenStorage = $this->client->getContainer()->get('security.token_storage');
        $this->assertNotNull($tokenStorage->getToken());

        $token = $tokenStorage->getToken();
        $this->assertEquals($this->twoFactorUser->getUsername(), $token->getUsername());
        $this->assertEquals(0, count($token->getRoles()), 'Tests if we have 0 roles when being partially authenticated');

        $this->client->request('GET', '/dashboard');
        $this->assertEquals('http://localhost/login/two_factor', $crawler->getUri(), 'Tests whether we land on the two factor page if we are partially authenticated and browsing to a secured page');

        $authenticator = new GoogleAuthenticator();
        $code = $authenticator->getCode($this->twoFactorUser->getGoogleAuthenticatorSecret());

        $button = $this->client->getCrawler()->filter('button[type=submit]')->first();
        $form = $button->form();

        $form['_auth_code'] = $code;

        $crawler = $this->client->submit($form);

        $this->assertEquals('http://localhost/dashboard', $crawler->getUri(), 'Tests whether we land on the dashboard after login');

        $tokenStorage = $this->client->getContainer()->get('security.token_storage');
        $this->assertNotNull($tokenStorage->getToken());

        $token = $tokenStorage->getToken();
        $this->assertEquals($this->twoFactorUser->getUsername(), $token->getUsername());
        $this->assertEquals($this->twoFactorUser->getRoles(), $this->getRoles($token->getRoles()), 'Tests if we have all roles we should have after login');
    }

    public function testCancelTwoFactorLogin() {
        $this->client->restart();

        $this->client->followRedirects(true);

        $crawler = $this->client->request('GET', '/dashboard');
        $this->assertEquals('http://localhost/login', $crawler->getUri(), 'Tests whether we land at the login page');

        $button = $crawler->filter('button[type=submit]')->first();
        $form = $button->form();

        $form['_username']->setValue('testuser2');
        $form['_password']->setValue('Test1234$');

        $crawler = $this->client->submit($form);
        $this->assertEquals('http://localhost/login/two_factor', $crawler->getUri(), 'Tests whether we land on the two factor page');

        $tokenStorage = $this->client->getContainer()->get('security.token_storage');
        $this->assertNotNull($tokenStorage->getToken());

        $token = $tokenStorage->getToken();
        $this->assertEquals($this->twoFactorUser->getUsername(), $token->getUsername());
        $this->assertEquals(0, count($token->getRoles()), 'Tests if we have 0 roles when being partially authenticated');

        $link = $crawler->filter('a[role=button]')->first()->link();
        $crawler = $this->client->click($link);

        $this->assertEquals('http://localhost/login', $crawler->getUri(), 'Tests if we land on the login page after cancelling two factor authentication');
    }

    private static function getRoles(array $roles) {
        return array_map(function(Role $role) {
            return $role->getRole();
        }, $roles);
    }
}