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
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/profile")
 */
class ProfileController extends AbstractController {

    use AttributeDataTrait;

    /**
     * @Route("", name="profile")
     */
    public function index(Request $request, AttributePersister $attributePersister, EntityManagerInterface $em, ConfirmationManager $confirmationManager) {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            if($form->has('group_email')) {
                $email = $form->get('group_email')->get('email')->getData();

                if(empty($email)) {
                    $user->setEmail(null);
                } else if($user->getEmail() !== $email) {
                    $confirmationManager->newConfirmation($user, $email);
                }
            }

            $em->persist($user);
            $em->flush();

            $attributeData = $this->getAttributeData($form);
            $attributePersister->persistUserAttributes($attributeData, $user);

            $this->addFlash('success', 'profile.success');
            return $this->redirectToRoute('profile');
        }

        if($confirmationManager->hasConfirmation($user)) {
            $this->addFlash('success', 'email_confirmation.sent');
        }

        return $this->render('profile/index.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * @Route("/password", name="profile_password")
     */
    public function changePassword(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder) {
        $this->denyAccessUnlessGranted(ProfileVoter::CHANGE_PASSWORD);

        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(PasswordChangeType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() && !$user instanceof ActiveDirectoryUser) {
            $password = $form->get('newPassword')->getData();
            $user->setPassword($passwordEncoder->encodePassword($user, $password));
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