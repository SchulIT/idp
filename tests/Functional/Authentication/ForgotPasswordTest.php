<?php

namespace App\Tests\Functional\Authentication;

use App\Entity\PasswordResetToken;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ForgotPasswordTest extends WebTestCase {
    /** @var EntityManagerInterface */
    private $em;

    /** @var TranslatorInterface */
    private $translator;

    /** @var Client */
    private $client;

    /** @var User */
    private $user;

    public function setUp() {
        $this->client = static::createClient();

        $this->em = $this->client->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->translator = $this->client->getContainer()
            ->get('translator');

        $this->user = (new User())
            ->setUsername('testuser')
            ->setFirstname('Test')
            ->setLastname('User')
            ->setIsActive(true)
            ->setEmail('testuser@school.it')
            ->setPassword('foo');

        $this->em->persist($this->user);
        $this->em->flush();
    }

    public function tearDown() {
        $this->em->close();
        $this->em = $this->user = null;

        parent::tearDown();
    }

    public function testForgotPasswordExistingUser() {
        /*
         * STEP 1: Navigate to /forgot_pw and fill out the form
         */
        $crawler = $this->client->request('GET', '/forgot_pw');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $button = $crawler->filter('button[type=submit]')->first();
        $form = $button->form();
        $form['_username']->setValue('testuser');

        /*
         * STEP 2: Submit the form
         */
        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect());

        $mailCollector = $this->client->getProfile()->getCollector('swiftmailer');
        $collectedMessages = $mailCollector->getMessages();

        $this->assertEquals(1, count($collectedMessages));

        $token = $this->em->getRepository(PasswordResetToken::class)
            ->findOneBy([
                'user' => $this->user
            ]);

        $this->assertNotNull($token);

        /** @var \Swift_Message $message */
        $message = $collectedMessages[0];
        $this->assertInstanceOf(\Swift_Message::class, $message);
        $this->assertSame(
            $this->translator->trans('reset_password.title', [], 'mail'),
            $message->getSubject()
        );
        $this->assertSame(
            $this->user->getEmail(),
            key($message->getTo())
        );
        /*$this->assertStringContainsString(
            $token->getToken(),
            $message->getBody()
        );*/

        /*
         * STEP 3: Be redirected to /login with propert success message
         */
        $crawler = $this->client->followRedirect();

        $this->assertEquals(1,
            $crawler->filter('div.bs-callout.bs-callout-success > p')->count()
        );

        $this->assertSame(
            $this->translator->trans('forgot_pw.request.success', [], 'security'),
            $crawler->filter('div.bs-callout.bs-callout-success > p')->first()->html()
        );

        /*
         * STEP 4: Navigate to /forgot_pw/{token}
         */
        $crawler = $this->client->request('GET', '/forgot_pw/' . $token->getToken());

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        /*
         * STEP 5A: Fill out the form with two non-identical passwords
         */


        /*
         * STEP 5B: Fill out the form with a non compliant password
         */

        /*
         * STEP 5C: Fill out the form with a compliant password
         */
        $button = $crawler->filter('button[type=submit]')->first();
        $form = $button->form();
        $form['_password']->setValue('auUTthtyP59wK-JC7szZ6Wbec');
        $form['_repeat_password']->setValue('auUTthtyP59wK-JC7szZ6Wbec');

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertEquals(1,
            $crawler->filter('div.bs-callout.bs-callout-success > p')->count()
        );

        $this->assertSame(
            $this->translator->trans('forgot_pw.change.success'),
            $crawler->filter('div.bs-callout.bs-callout-success > p')->first()->html()
        );

        /*
         * STEP 6: Check if the password was really changed
         */
        $this->em->clear();
        $user = $this->em->getRepository(User::class)
            ->findOneBy(['id' => $this->user->getId()]);

        $this->assertNotNull($user);
        $this->assertNotEquals('foo', $user->getPassword());

        /** @var UserPasswordEncoderInterface $encoder */
        $encoder = $this->client->getContainer()->get('password_encoder');

        $this->assertTrue($encoder->isPasswordValid($user, 'auUTthtyP59wK-JC7szZ6Wbec'));
    }

}