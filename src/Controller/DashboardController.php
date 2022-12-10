<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LinkStudentType;
use App\Repository\RegistrationCodeRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Security\Voter\LinkStudentVoter;
use App\Service\UserServiceProviderResolver;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class DashboardController extends AbstractController {

    #[Route(path: '/')]
    public function redirectToDashboard(): Response {
        return $this->redirectToRoute('dashboard');
    }

    #[Route(path: '/dashboard', name: 'dashboard')]
    public function dashboard(Request $request, UserServiceProviderResolver $resolver): Response {
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

    #[Route(path: '/link', name: 'link_student')]
    public function index(Request $request, UserRepositoryInterface $userRepository, DateHelper $dateHelper,
                          RegistrationCodeRepositoryInterface $codeRepository, TranslatorInterface $translator): Response {
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
                }
                else if($user->getLinkedStudents()->contains($code->getStudent())) {
                    $this->addFlash('error', 'link.student.error.already_linked');
                } if($code->getRedeemingUser() !== null) {
                    $this->addFlash('error', $translator->trans('register.redeem.error.already_redeemed', [], 'security'));
                } else {
                    $user->addLinkedStudent($code->getStudent());
                    $code->setRedeemingUser($user);

                    $userRepository->persist($user);
                    $codeRepository->persist($code);

                    $this->addFlash('success', 'link.student.success');
                }
            } else {
                $this->addFlash('error', $translator->trans('register.redeem.error.not_found', [], 'security'));
            }
        }

        return $this->redirectToRoute('dashboard');
    }
}