<?php

namespace App\Controller\Api;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractApiController {
    /**
     * Default API controller (only used for testing purporses)
     */
    #[Route(path: '/api', name: 'api_default')]
    public function index(): Response {
        return $this->returnJson([]);
    }
}