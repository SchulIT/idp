<?php

namespace App\Controller\Api;

use App\Repository\UserTypeRepositoryInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\UserType;

/**
 * @Route("/api")
 */
class UserTypeController extends AbstractApiController {

    /**
     * Returns a list of user types which a user can be assigned.
     *
     * @Route("/user_types", methods={"GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns a list of user types which a user can be assigned.",
     *     @Model(type=UserType::class)
     * )
     */
    public function getUserTypes(UserTypeRepositoryInterface $repository) {
        $types = $repository->findAll();

        return $this->json($types);
    }
}