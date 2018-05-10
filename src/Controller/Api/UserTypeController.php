<?php

namespace App\Controller\Api;

use App\Entity\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserTypeController extends Controller {

    /**
     * Returns a list of user types which a user can be assigned.
     *
     * @Route("/api/user_types")
     * @Method("GET")
     */
    public function getUserTypes() {
        $types = $this->getDoctrine()->getRepository(UserType::class)
            ->findAll();

        return $this->json($types);
    }
}