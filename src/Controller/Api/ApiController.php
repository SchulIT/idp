<?php

namespace App\Controller\Api;

use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractApiController {
    /**
     * Default API controller (only used for testing purporses)
     *
     * @Route("/api", name="api_default")
     */
    public function index() {
        return $this->returnJson([]);
    }
}