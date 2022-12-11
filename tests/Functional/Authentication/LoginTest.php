<?php

namespace App\Tests\Functional\Authentication;

use App\Entity\User;
use App\Entity\UserType;
use Doctrine\ORM\EntityManagerInterface;
use OTPHP\TOTP;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Http\Authenticator\Token\PostAuthenticationToken;

class LoginTest extends WebTestCase {

    /** @var EntityManagerInterface */
    private $em;

    /** @var KernelBrowser */
    private $client;

    /** @var User */
    private $user;

    /** @var User */
    private $twoFactorUser;

    /** @var UserType */
    private $userType;

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

    public function tearDown(): void {
        $this->em->close();
        $this->em = $this->user = $this->twoFactorUser = $this->userType = null;

        parent::tearDown();
    }

    public function testLoginNoTwoFactor() {
        $this->client->restart();

        $this->client->followRedirects(true);

        $crawler = $this->client->request('GET', '/dashboard');
        $this->assertEquals('http://localhost/login', $crawler->getUri(), 'Tests whether we land at the login page');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), 'Ensure that we have a HTTP 200 at the login page');

        $button = $crawler->filter('button[type=submit]')->first();
        $form = $button->form();

        $form['_username']->setValue('testuser');
        $form['_password']->setValue('Test1234$');

        $crawler = $this->client->submit($form);
        $this->assertEquals('http://localhost/dashboard', $crawler->getUri(), 'Tests whether we land on the dashboard after successful login');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), 'Ensure that we have a HTTP 200 at the dashboard');

        $tokenStorage = $this->client->getContainer()->get('security.token_storage');

        $this->assertNotNull($tokenStorage->getToken());

        $token = $tokenStorage->getToken();

        $this->assertInstanceOf(PostAuthenticationToken::class, $token);
        $this->assertEquals($this->user->getUserIdentifier(), $token->getUserIdentifier());
    }

    public function testLoginTwoFactor() {
        $this->client->restart();

        $this->client->followRedirects(true);

        $crawler = $this->client->request('GET', '/dashboard');
        $this->assertEquals('http://localhost/login', $crawler->getUri(), 'Tests whether we land at the login page');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), 'Ensure that we have a HTTP 200 at the login page');

        $button = $crawler->filter('button[type=submit]')->first();
        $form = $button->form();

        $form['_username']->setValue('testuser2');
        $form['_password']->setValue('Test1234$');

        $crawler = $this->client->submit($form);
        $this->assertEquals('http://localhost/login/2fa', $crawler->getUri(), 'Tests whether we land on the two factor page');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), 'Ensure that we have a HTTP 200 at the two factor page');

        $tokenStorage = $this->client->getContainer()->get('security.token_storage');
        $this->assertNotNull($tokenStorage->getToken());

        $token = $tokenStorage->getToken();
        $this->assertEquals($this->twoFactorUser->getUserIdentifier(), $token->getUserIdentifier());

        $this->client->request('GET', '/dashboard');
        $this->assertEquals('http://localhost/login/2fa', $crawler->getUri(), 'Tests whether we land on the two factor page if we are partially authenticated and browsing to a secured page');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), 'Ensure that we have a HTTP 200 at the two factor page (2)');


        $code = TOTP::create($this->twoFactorUser->getGoogleAuthenticatorSecret())->now();

        $button = $this->client->getCrawler()->filter('button[type=submit]')->first();
        $form = $button->form();

        $form['_auth_code'] = $code;

        $crawler = $this->client->submit($form);

        $this->assertEquals('http://localhost/dashboard', $crawler->getUri(), 'Tests whether we land on the dashboard after login');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), 'Ensure that we have a HTTP 200 at the dashboard');

        $tokenStorage = $this->client->getContainer()->get('security.token_storage');
        $this->assertNotNull($tokenStorage->getToken());

        $token = $tokenStorage->getToken();
        $this->assertInstanceOf(PostAuthenticationToken::class, $token);
        $this->assertEquals($this->twoFactorUser->getUserIdentifier(), $token->getUserIdentifier());
    }

    public function testCancelTwoFactorLogin() {
        $this->client->restart();

        $this->client->followRedirects(true);

        $crawler = $this->client->request('GET', '/dashboard');
        $this->assertEquals('http://localhost/login', $crawler->getUri(), 'Tests whether we land at the login page');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), 'Ensure that we have a HTTP 200 at the login page');

        $button = $crawler->filter('button[type=submit]')->first();
        $form = $button->form();

        $form['_username']->setValue('testuser2');
        $form['_password']->setValue('Test1234$');

        $crawler = $this->client->submit($form);
        $this->assertEquals('http://localhost/login/2fa', $crawler->getUri(), 'Tests whether we land on the two factor page');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), 'Ensure that we have a HTTP 200 at the two factor page');

        $tokenStorage = $this->client->getContainer()->get('security.token_storage');
        $this->assertNotNull($tokenStorage->getToken());

        $token = $tokenStorage->getToken();
        $this->assertEquals($this->twoFactorUser->getUserIdentifier(), $token->getUserIdentifier());
        $this->assertEquals(0, count($token->getRoles()), 'Tests if we have 0 roles when being partially authenticated');

        $link = $crawler->filter('a[role=button]')->first()->link();
        $crawler = $this->client->click($link);

        $this->assertEquals('http://localhost/logout/success', $crawler->getUri(), 'Tests if we land on the logout page after cancelling two factor authentication');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), 'Ensure that we have a HTTP 200 at the logout page');
    }
}