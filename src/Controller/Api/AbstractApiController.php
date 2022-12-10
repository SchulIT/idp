<?php

namespace App\Controller\Api;

use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class AbstractApiController extends AbstractController {

    public function __construct(protected SerializerInterface $serializer)
    {
    }

    public function returnJson($data, int $status = 200, array $headers = []): JsonResponse {
        $json = $this->serializer->serialize($data, 'json');

        return new JsonResponse($json, $status, $headers, true);
    }
}