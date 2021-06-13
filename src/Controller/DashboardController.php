<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LinkStudentType;
use App\Link\LinkStudentsHelper;
use App\Link\NotAStudentException;
use App\Security\UserAuthenticator;
use App\Security\Voter\LinkStudentVoter;
use App\Service\UserServiceProviderResolver;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class DashboardController extends AbstractController {

    /**
     * @Route("/")
     */
    public function redirectToDashboard() {
        return $this->redirectToRoute('dashboard');
    }

    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function dashboard(Request $request, UserServiceProviderResolver $resolver,
                              LinkStudentsHelper $linkStudentsHelper) {
        /** @var User $user */
        $user = $this->getUser();

        $services = $resolver->getServicesForCurrentUser();
        $linkedUsers = $linkStudentsHelper->getLinks($user);

        $form = null;

        if($this->isGranted(LinkStudentVoter::LINK)) {
            $form = $this->createForm(LinkStudentType::class);
            $form->handleRequest($request);
        }

        return $this->render('dashboard/index.html.twig', [
            'services' => $services,
            'links' => $linkedUsers,
            'links_required' => $user->getType()->isCanLinkStudents() && count($linkedUsers) === 0,
            'form' => $form !== null ? $form->createView() : null
        ]);
    }

    /**
     * @Route("/link", name="link_student")
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
                /** @var User $student */
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
        }

        return $this->redirectToRoute('dashboard');
    }
}