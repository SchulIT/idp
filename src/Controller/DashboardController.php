<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DashboardController extends Controller {

    /**
     * @Route("/")
     */
    public function redirectToDashboard() {
        return $this->redirectToRoute('dashboard');
    }

    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function dashboard() {
        $services = $this->get('user.sp_resolver')->getServicesForCurrentUser();

        return $this->render('dashboard/index.html.twig', [
            'services' => $services
        ]);
    }
}