<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController {
    /**
     * Default API controller (only used for testing purporses)
     */
    #[Route(path: '/api', name: 'api_default')]
    public function index(): Response {
        return $this->json([]);
    }
}