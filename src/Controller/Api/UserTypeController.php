<?php

namespace App\Controller\Api;

use App\Repository\UserTypeRepositoryInterface;
use App\Response\ListUserTypeResponse;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

/**
 * @Route("/api")
 */
class UserTypeController extends AbstractApiController {

    /**
     * Returns a list of user types which a user can be assigned.
     *
     * @Route("/user_types", methods={"GET"})
     * @OA\Get(operationId="api_usertypes_list")
     * @OA\Response(
     *     response="200",
     *     description="Returns a list of user types which a user can be assigned.",
     *     @Model(type=ListUserTypeResponse::class)
     * )
     */
    public function getUserTypes(UserTypeRepositoryInterface $repository): Response {
        $types = $repository->findAll();

        return $this->returnJson(
            new ListUserTypeResponse($types)
        );
    }
}