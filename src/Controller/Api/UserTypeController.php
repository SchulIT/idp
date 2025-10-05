<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\UserType;
use App\Repository\UserTypeRepositoryInterface;
use App\Response\ListUserTypeResponse;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use App\Response\UserType as UserTypeResponse;

class UserTypeController extends AbstractController
{
    /**
     * Abfrage aller Benutzertypen im System.
     */
    #[OA\Get(operationId: 'api_usertypes_list', tags: ['Benutzertypen'])]
    #[OA\Response(response: '200', description: 'Liste mit Benutzertypen', content: new Model(type: ListUserTypeResponse::class))]
    #[Route(path: '/api/user_types', methods: ['GET'])]
    public function getUserTypes(UserTypeRepositoryInterface $repository): Response {
        $types = array_map(
            fn(UserType $type): \App\Response\UserType => $this->transformResponse($type),
            $repository->findAll()
        );

        return $this->json(
            new ListUserTypeResponse($types)
        );
    }
    private function transformResponse(UserType $entity): UserTypeResponse {
        return new UserTypeResponse($entity->getUuid()->toString(), $entity->getName(), $entity->getAlias(), $entity->getEduPerson());
    }
}
