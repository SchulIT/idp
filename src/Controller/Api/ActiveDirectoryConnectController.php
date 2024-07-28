<?php

namespace App\Controller\Api;

use App\Entity\ActiveDirectoryUser;
use App\Repository\UserRepositoryInterface;
use App\Request\ActiveDirectoryUserRequest;
use App\Response\ErrorResponse;
use App\Response\ListActiveDirectoryUserResponse;
use App\Response\ViolationListResponse;
use App\Security\ActiveDirectoryUserInformation;
use App\Security\UserCreator;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Response\ActiveDirectoryUser as ActiveDirectoryUserResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Endpunkte für den Active Directory Connect Client
 */
#[Route(path: '/api/ad_connect')]
#[IsGranted('ROLE_ADCONNECT')]
class ActiveDirectoryConnectController extends AbstractController {

    public function __construct(private readonly UserCreator $userCreator, private readonly UserRepositoryInterface $repository) { }

    /**
     * Gibt die Liste aller Benutzer zurück, die über den Active Directory Connect Client provisioniert wurden. Benutzer,
     * die gelöscht (aber nicht endgültig gelöscht sind), werden hier nicht berücksichtigt.
     */
    #[OA\Get(operationId: 'api_adconnect_list_users', tags: [ 'Active Directory Connect Client'])]
    #[OA\Response(
        response: "200",
        description: "Liste der Active Directory Benutzer. Hinweis: Gelöschte Benutzer (die sich im Papierkorb befinden), werden nicht zurückgegeben.",
        content: new Model(type: ListActiveDirectoryUserResponse::class )
    )]
    #[Route(path: '', methods: ['GET'])]
    public function list(): Response {
        $users = array_map(
            fn(ActiveDirectoryUser $user) => $this->transformResponse($user),
            array_filter(
                $this->repository->findAllActiveDirectoryUsers(),
                fn(ActiveDirectoryUser $user) => $user->isDeleted() === false
            )
        );
        return $this->json(new ListActiveDirectoryUserResponse($users));
    }

    /**
     * Benutzer erstellen
     */
    #[OA\Post(operationId: 'api_adconnect_new_user', tags: [ 'Active Directory Connect Client'])]
    #[OA\RequestBody(content: new Model(type: ActiveDirectoryUserRequest::class))]
    #[OA\Response(response: '201', description: 'Benutzer wurde erfolgreich angelegt.')]
    #[OA\Response(response: '400', description: 'Validierung fehlgeschlagen.', content: new Model(type:ViolationListResponse::class))]
    #[OA\Response(response: '500', description: 'Serverfehler', content: new Model(type: ErrorResponse::class))]
    #[Route(path: '', methods: ['POST'])]
    public function add(ActiveDirectoryUserRequest $request): Response {
        $userInfo = $this->transformRequest($request);

        if($this->userCreator->canCreateUser($userInfo)) {
            $user = $this->userCreator->createUser($userInfo);
            $user->setDeletedAt(null); // Adds ability to restore users from Active Directory Connect
            $this->repository->persist($user);

            return new Response(null, Response::HTTP_CREATED);
        }

        return $this->json(
            new ErrorResponse('Cannot create user. Specify a sync rule first.')
        );
    }

    /**
     * Benutzer aktualisieren
     */
    #[OA\Patch(operationId: 'api_adconnect_update_user', tags: [ 'Active Directory Connect Client'])]
    #[OA\RequestBody(content: new Model(type:ActiveDirectoryUserRequest::class))]
    #[OA\Response(response: '200', description: 'Benutzer wurde erfolgreich aktualisiert.')]
    #[OA\Response(response: '400', description: 'Validierung fehlgeschlagen.', content: new Model(type:ViolationListResponse::class))]
    #[OA\Response(response: '500', description: 'Serverfehler', content: new Model(type: ErrorResponse::class))]
    #[Route(path: '/{objectGuid}', methods: ['PATCH'])]
    public function update(ActiveDirectoryUser $user, ActiveDirectoryUserRequest $request): Response {
        $user = $this->userCreator->createUser($this->transformRequest($request), $user);
        $this->repository->persist($user);
        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Benutzer löschen
     */
    #[OA\Delete(operationId: 'api_adconnect_delete_user', tags: [ 'Active Directory Connect Client'])]
    #[OA\Response(response: '204', description: 'Benutzer wurde erfolgreich gelöscht.')]
    #[OA\Response(response: '404', description: 'Benutzer wurde nicht gefunden.')]
    #[OA\Response(response: '500', description: 'Serverfehler', content: new Model(type: ErrorResponse::class))]
    #[Route(path: '/{objectGuid}', methods: ['DELETE'])]
    public function remove(ActiveDirectoryUser $user): Response {
        $this->repository->remove($user);
        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    private function transformRequest(ActiveDirectoryUserRequest $request): ActiveDirectoryUserInformation {
        return (new ActiveDirectoryUserInformation())
            ->setUsername($request->getSamAccountName())
            ->setUserPrincipalName($request->getUserPrincipalName())
            ->setFirstname($request->getFirstname())
            ->setLastname($request->getLastname())
            ->setEmail($request->getEmail())
            ->setGuid($request->getObjectGuid())
            ->setOu($request->getOu())
            ->setGroups($request->getGroups());
    }

    private function transformResponse(ActiveDirectoryUser $user): ActiveDirectoryUserResponse {
        return new ActiveDirectoryUserResponse($user->getUserIdentifier(), $user->getFirstname(), $user->getLastname(), $user->getGrade(), $user->getObjectGuid());
    }
}