<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class AbstractApiController extends Controller {
    public function returnJson($data, int $status = 200, array $headers = [], array $context = []): JsonResponse {
        $serializer = $this->get('jms_serializer');
        $json = $serializer->serialize($data, 'json');

        return new JsonResponse($json, $status, $headers, true);
    }
}