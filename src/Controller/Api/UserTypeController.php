<?php

namespace App\Controller\Api;

use App\Entity\UserType;
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
    public function getUserTypes() {
        $types = $this->getDoctrine()->getRepository(UserType::class)
            ->findAll();

        return $this->json($types);
    }
}