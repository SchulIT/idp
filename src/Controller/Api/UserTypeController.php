<?php

namespace App\Controller\Api;

use App\Entity\UserType;
use App\Repository\UserTypeRepositoryInterface;
use App\Response\ListUserTypeResponse;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use App\Response\UserType as UserTypeResponse;

#[Route(path: '/api')]
class UserTypeController extends AbstractController {

    /**
     * Abfrage aller Benutzertypen im System.
     *
     * Returns a list of user types which a user can be assigned.
     */
    #[OA\Get(operationId: 'api_usertypes_list', tags: ['Allgemein'])]
    #[OA\Response(response: '200', description: 'Liste mit Benutzertypen', content: new Model(type: ListUserTypeResponse::class))]
    #[Route(path: '/user_types', methods: ['GET'])]
    public function getUserTypes(UserTypeRepositoryInterface $repository): Response {
        $types = $repository->findAll();

        return $this->json(
            new ListUserTypeResponse($types)
        );
    }

    private function transformResponse(UserType $entity): UserTypeResponse {
        return new UserTypeResponse($entity->getUuid()->toString(), $entity->getName(), $entity->getAlias(), $entity->getEduPerson());
    }
}