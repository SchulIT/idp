<?php

namespace App\Controller;

use App\Entity\PrivacyPolicy;
use App\Entity\User;
use App\Form\PricacyPolicyType;
use App\Repository\PrivacyPolicyRepositoryInterface;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PrivacyPolicyController extends AbstractController {

    public function __construct(private PrivacyPolicyRepositoryInterface $repository)
    {
    }

    #[Route(path: '/register/privacy_policy', name: 'register_privacy_policy')]
    public function showRegister(): Response {
        $policy = $this->repository->findOne() ?? new PrivacyPolicy();

        return $this->render('register/privacy_policy.html.twig', [
            'policy' => $policy
        ]);
    }

    #[Route(path: '/privacy_policy', name: 'show_privacy_policy')]
    public function show(): Response {
        /** @var User $user */
        $user = $this->getUser();
        $policy = $this->repository->findOne();

        $mustConfirm = $policy !== null && $user->getPrivacyPolicyConfirmedAt() < $policy->getChangedAt();

        return $this->render('privacy/show.html.twig', [
            'policy' => $policy,
            'must_confirm' => $mustConfirm
        ]);
    }

    #[Route(path: '/admin/privacy_policy', name: 'edit_privacy_policy')]
    public function edit(Request $request): Response {
        $policy = $this->repository->findOne() ?? new PrivacyPolicy();
        $form = $this->createForm(PricacyPolicyType::class, $policy);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            if($request->request->has('submit-change')) {
                $policy->setChangedAt(new DateTime());
            }

            $this->repository->persist($policy);

            $this->addFlash('success', 'privacy_policy.success');
            return $this->redirectToRoute('edit_privacy_policy');
        }

        return $this->render('privacy/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}