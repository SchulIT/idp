<?php

namespace App\Controller\Api;

use App\Repository\UserTypeRepositoryInterface;
use App\Response\ListUserTypeResponse;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

#[Route(path: '/api')]
class UserTypeController extends AbstractApiController {

    /**
     * Returns a list of user types which a user can be assigned.
     *
     * @OA\Get(operationId="api_usertypes_list")
     * @OA\Response(
     *     response="200",
     *     description="Returns a list of user types which a user can be assigned.",
     *     @Model(type=ListUserTypeResponse::class)
     * )
     */
    #[Route(path: '/user_types', methods: ['GET'])]
    public function getUserTypes(UserTypeRepositoryInterface $repository): Response {
        $types = $repository->findAll();

        return $this->returnJson(
            new ListUserTypeResponse($types)
        );
    }
}