<?php

namespace App\Controller;

use AdAuth\AdAuthInterface;
use AdAuth\Credentials;
use AdAuth\Response\AuthenticationResponse;
use AdAuth\SocketException;
use App\Entity\User;
use App\Form\LinkStudentType;
use App\Link\LinkStudentsHelper;
use App\Link\NotAStudentException;
use App\Repository\UserRepositoryInterface;
use App\Security\UserAuthenticator;
use App\Security\Voter\LinkStudentVoter;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/link")
 */
class LinkStudentController extends AbstractController {
    /**
     * @Route("", name="link_student")
     */
    public function index(UserAuthenticator $authenticator, UserProviderInterface $userProvider, CsrfTokenManagerInterface $tokenManager, Request $request,
                          LinkStudentsHelper $linkStudentsHelper, TranslatorInterface $translator, LoggerInterface $logger) {
        $this->denyAccessUnlessGranted(LinkStudentVoter::LINK);

        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(LinkStudentType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $username = $form->get('username')->getData();

            try {
                $csrfToken = $tokenManager->getToken('authenticate');
                $credentials = [
                    'username' => $username,
                    'password' => $form->get('password')->getData(),
                    'csrf_token' => $csrfToken->getValue()
                ];
                $student = $authenticator->getUser($credentials, $userProvider);

                $linkStudentsHelper->link($user, $student);
                $this->addFlash('success', 'link.student.success');
            } catch(NotAStudentException $e) {
                $logger->info('Failed linking student - user is not a student.', [
                    'username' => $username
                ]);
                $this->addFlash('error', 'link.student.error.no_student');
            } catch (AuthenticationException $e) {
                $logger->error('Failed linking student - authentication error.', [
                    'exception' => $e,
                    'username' => $username
                ]);
                $this->addFlash('error', $translator->trans($e->getMessage(), [], 'security'));
            }

            return $this->redirectToRoute('link_student');
        }

        return $this->render('link/student.html.twig', [
            'form' => $form->createView(),
            'links' => $linkStudentsHelper->getLinks($user)
        ]);
    }
}