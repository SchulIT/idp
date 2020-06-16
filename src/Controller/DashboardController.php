<?php

namespace App\Controller;

use App\Entity\User;
use App\Link\LinkStudentsHelper;
use App\Service\UserServiceProviderResolver;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

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
    public function dashboard(UserServiceProviderResolver $resolver, LinkStudentsHelper $linkStudentsHelper) {
        /** @var User $user */
        $user = $this->getUser();

        $services = $resolver->getServicesForCurrentUser();
        $linkedUsers = $linkStudentsHelper->getLinks($user);

        return $this->render('dashboard/index.html.twig', [
            'services' => $services,
            'links' => $linkedUsers
        ]);
    }
}