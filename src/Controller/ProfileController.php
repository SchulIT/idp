<?php

namespace App\Controller;

use App\Entity\ActiveDirectoryUser;
use App\Entity\User;
use App\Form\AttributeDataTrait;
use App\Form\ProfileType;
use App\Security\EmailConfirmation\ConfirmationManager;
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
    public function index(Request $request, UserPasswordEncoderInterface $passwordEncoder, AttributePersister $attributePersister,
                          EntityManagerInterface $em, ConfirmationManager $confirmationManager) {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            if($form->has('group_password')) {
                $password = $form->get('group_password')->get('password')->getData();

                if (!empty($password) && !$user instanceof ActiveDirectoryUser) {
                    $user->setPassword($passwordEncoder->encodePassword($user, $password));
                    $user->setMustChangePassword(false);
                }
            }

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
}