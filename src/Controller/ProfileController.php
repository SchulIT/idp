<?php

namespace App\Controller;

use App\Entity\ActiveDirectoryUser;
use App\Entity\User;
use App\Form\AttributeDataTrait;
use App\Form\PasswordChangeType;
use App\Form\ProfileType;
use App\Security\EmailConfirmation\ConfirmationManager;
use App\Security\Voter\ProfileVoter;
use App\Service\AttributePersister;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route(path: '/profile')]
class ProfileController extends AbstractController {

    use AttributeDataTrait;

    #[Route(path: '', name: 'profile')]
    public function index(#[CurrentUser] User $user, Request $request, AttributePersister $attributePersister, EntityManagerInterface $em, ConfirmationManager $confirmationManager): Response {
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();

            if(empty($email)) {
                $user->setEmail(null);
            } else if($user->getEmail() !== $email) {
                $confirmationManager->newConfirmation($user, $email);
            }

            $em->persist($user);
            $em->flush();

            $attributeData = $this->getAttributeData($form);
            $attributePersister->persistUserAttributes($attributeData, $user);

            $this->addFlash('success', 'profile.success');
            return $this->redirectToRoute('profile');
        }

        if($confirmationManager->hasConfirmation($user)) {
            $this->addFlash('success', 'email_confirmation.pending');
        }

        return $this->render('profile/index.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    #[Route(path: '/password', name: 'profile_password')]
    public function changePassword(#[CurrentUser] User $user, Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response {
        $this->denyAccessUnlessGranted(ProfileVoter::CHANGE_PASSWORD);

        $form = $this->createForm(PasswordChangeType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() && !$user instanceof ActiveDirectoryUser) {
            $password = $form->get('newPassword')->getData();
            $user->setPassword($passwordHasher->hashPassword($user, $password));
            $user->setMustChangePassword(false);

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'profile.change_password.success');
            return $this->redirectToRoute('profile_password');
        }

        return $this->render('profile/change_password.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
            'can_change_password' => !$user instanceof ActiveDirectoryUser
        ]);
    }
}