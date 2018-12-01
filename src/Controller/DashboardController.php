<?php

namespace App\Controller;

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
    public function dashboard(UserServiceProviderResolver $resolver) {
        $services = $resolver->getServicesForCurrentUser();

        return $this->render('dashboard/index.html.twig', [
            'services' => $services
        ]);
    }
}