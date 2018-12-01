<?php

namespace App\Controller\Api;

use App\Repository\UserTypeRepositoryInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
class UserTypeController extends AbstractApiController {

    /**
     * Returns a list of user types which a user can be assigned.
     *
     * @Route("/user_types", methods={"GET"})
     */
    public function getUserTypes(UserTypeRepositoryInterface $repository) {
        $types = $repository->findAll();

        return $this->json($types);
    }
}