<?php

namespace App\Controller;

use App\Entity\ActiveDirectoryUser;
use App\Form\AttributeDataTrait;
use App\Form\ProfileType;
use App\Service\AttributePersister;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/profile")
 */
class ProfileController extends Controller {

    use AttributeDataTrait;

    /**
     * @Route("", name="profile")
     */
    public function index(Request $request, UserPasswordEncoderInterface $passwordEncoder, AttributePersister $attributePersister) {
        $user = $this->getUser();

        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('group_password')->get('password')->getData();

            if(!empty($password) && !$user instanceof ActiveDirectoryUser) {
                $user->setPassword($passwordEncoder->encodePassword($user, $password));
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $attributeData = $this->getAttributeData($form);
            $attributePersister->persistUserAttributes($attributeData, $user);

            $this->addFlash('success', 'profile.success');
            return $this->redirectToRoute('profile');
        }

        return $this->render('profile/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}