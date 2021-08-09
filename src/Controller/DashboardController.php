<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LinkStudentType;
use App\Link\LinkStudentsHelper;
use App\Link\NotAStudentException;
use App\Repository\RegistrationCodeRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Security\UserAuthenticator;
use App\Security\Voter\LinkStudentVoter;
use App\Service\UserServiceProviderResolver;
use Psr\Log\LoggerInterface;
use SchulIT\CommonBundle\Helper\DateHelper;
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
    public function dashboard(Request $request, UserServiceProviderResolver $resolver) {
        /** @var User $user */
        $user = $this->getUser();

        $services = $resolver->getServicesForCurrentUser();

        $form = null;

        if($this->isGranted(LinkStudentVoter::LINK)) {
            $form = $this->createForm(LinkStudentType::class);
            $form->handleRequest($request);
        }

        return $this->render('dashboard/index.html.twig', [
            'services' => $services,
            'students' => $user->getLinkedStudents(),
            'links_required' => $user->getType()->isCanLinkStudents() && $user->getLinkedStudents()->count() === 0,
            'form' => $form !== null ? $form->createView() : null
        ]);
    }

    /**
     * @Route("/link", name="link_student")
     */
    public function index(Request $request, UserRepositoryInterface $userRepository, DateHelper $dateHelper,
                          RegistrationCodeRepositoryInterface $codeRepository, TranslatorInterface $translator) {
        $this->denyAccessUnlessGranted(LinkStudentVoter::LINK);

        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(LinkStudentType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() && !empty($form->get('code')->getData())) {
            $code = $codeRepository->findOneByCode($form->get('code')->getData());

            if($code !== null) {
                if($code->getValidFrom() !== null && $code->getValidFrom() > $dateHelper->getToday()) {
                    $this->addFlash('error', $translator->trans('register.redeem.error.not_yet_valid', [
                        '%date%' => $code->getValidFrom()->format($translator->trans('date.format'))
                    ], 'security'));
                } else {
                    if($user->getLinkedStudents()->contains($code->getStudent())) {
                        $this->addFlash('error', 'link.student.error.already_linked');
                    } else {
                        $user->addLinkedStudent($code->getStudent());
                        $code->setRedeemingUser($user);

                        $userRepository->persist($user);
                        $codeRepository->persist($code);

                        $this->addFlash('success', 'link.student.success');
                    }
                }
            } else {
                $this->addFlash('error', $translator->trans('register.redeem.error.not_found', [], 'security'));
            }
        }

        return $this->redirectToRoute('dashboard');
    }
}