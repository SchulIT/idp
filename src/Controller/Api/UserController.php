<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\ActiveDirectoryUser;
use App\Entity\User;
use App\Entity\UserType;
use App\Repository\UserRepositoryInterface;
use App\Repository\UserTypeRepositoryInterface;
use App\Request\UserAttributeRequest;
use App\Request\UserRequest;
use App\Response\ErrorResponse;
use App\Response\ListUserResponse;
use App\Response\Violation;
use App\Response\ViolationListResponse;
use App\Service\AttributePersister;
use Exception;
use JMS\Serializer\Exception\ValidationFailedException;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Override;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    public function __construct(private readonly ValidatorInterface $validator, private readonly UserRepositoryInterface $userRepository,
                                private readonly SerializerInterface $serializer,
                                private readonly UserTypeRepositoryInterface $userTypeRepository, private readonly AttributePersister $attributePersister) {    }
    #[Override]
    public function json(mixed $data, int $status = 200, array $headers = [], array $context = []): JsonResponse {
        $json = $this->serializer->serialize($data, 'json');
        return new JsonResponse($json, $status, $headers, true);
    }
    /**
     * Abfrage aller Benutzer im System
     */
    #[OA\Get(operationId: 'api_user_list', tags: ['Benutzer'])]
    #[OA\Response(response: '200', description: 'Liste mit Benutzern.', content: new Model(type: ListUserResponse::class))]
    #[OA\Parameter(name: 'offset', description: '[Pagination] Offset für das erste zurückgegebene Element.', in: 'query', required: false)]
    #[OA\Parameter(name: 'limit', description: '[Pagination] Anzahl der Benutzer, die zurückgegeben werden sollen.', in: 'query', required: true)]
    #[Route(path: '/api/user', methods: ['GET'])]
    public function list(Request $request, UserRepositoryInterface $userRepository): Response {
        $offset = $request->query->getInt('offset', 0);
        $limit = $request->query->getInt('limit', 50);

        if($offset < 0) {
            $offset = 0;
        }

        if($limit < 0) {
            $limit = 50;
        }

        $uuids = $userRepository->findAllUuids($offset, $limit, true);
        return $this->json(new ListUserResponse($uuids));
    }
    /**
     * Liefert Informationen über einen Benutzer. Dabei wird entweder dessen UUID angegeben oder alternativ die externe ID,
     * welche beim Import angegeben wurde.
     */
    #[OA\Get(operationId: 'api_user_get', tags: ['Benutzer'])]
    #[OA\Response(response: '200', description: 'Das Benutzerobjekt', content: new Model(type: User::class))]
    #[OA\Response(response: '404', description: 'Der Benutzer wurde nicht gefunden.')]
    #[Route(path: '/api/user/{uuidOrExternalId}', methods: ['GET'])]
    public function user(string $uuidOrExternalId): Response {
        $user = $this->getUserOrThrowNotFound($uuidOrExternalId);
        return $this->json($user);
    }
    /**
     * Einen neuen Benutzer erstellen.
     */
    #[OA\Post(operationId: 'api_users_new', tags: ['Benutzer'])]
    #[OA\RequestBody(content: new Model(type: UserRequest::class))]
    #[OA\Response(response: '201', description: 'Benutzer wurde angelegt.')]
    #[OA\Response(response: '400', description: 'Validierung fehlgeschlagen.', content: new Model(type:ViolationListResponse::class))]
    #[OA\Response(response: '500', description: 'Serverfehler', content: new Model(type: ErrorResponse::class))]
    #[Route(path: '/api/user', methods: ['POST'])]
    public function add(#[MapRequestPayload] UserRequest $request): Response {
        $violations = [ ];
        $existingUser = $this->userRepository->findOneByUsername($request->getUsername());

        if($existingUser instanceof User) {
            $violations[] = new Violation('username', 'Username already in use.');
        }

        $existingUser = $this->userRepository->findOneByEmail($request->getEmail());

        if($existingUser instanceof User) {
            $violations[] = new Violation('email', 'Email address already in use.');
        }

        if(count($violations) > 0) {
            return $this->json(
                new ViolationListResponse($violations),
                Response::HTTP_BAD_REQUEST
            );
        }

        $user = $this->transformRequest($request);
        $violations = $this->validator->validate($user);

        if(count($violations) > 0) {
            throw new ValidationFailedException($violations);
        }

        $this->userRepository->persist($user);

        return new Response(null, Response::HTTP_CREATED);
    }
    /**
     * Aktualisiert Informationen über einen Benutzer.
     */
    #[OA\Patch(operationId: 'api_users_update', tags: ['Benutzer'])]
    #[OA\RequestBody(content: new Model(type: UserRequest::class))]
    #[OA\Response(response: '204', description: 'Benutzer wurde aktualisiert.')]
    #[OA\Response(response: '400', description: 'Validierung fehlgeschlagen.', content: new Model(type:ViolationListResponse::class))]
    #[OA\Response(response: '404', description: 'Benutzer wurde nicht gefunden.')]
    #[OA\Response(response: '500', description: 'Serverfehler', content: new Model(type: ErrorResponse::class))]
    #[Route(path: '/api/user/{uuidOrExternalId}', methods: ['PATCH'])]
    public function update(string $uuidOrExternalId, #[MapRequestPayload] UserRequest $request): Response {
        $user = $this->getUserOrThrowNotFound($uuidOrExternalId);

        $user = $this->transformRequest($request, $user);
        $violations = $this->validator->validate($user);

        if(count($violations) > 0) {
            throw new ValidationFailedException($violations);
        }

        $this->userRepository->persist($user);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
    /**
     * Aktualisiert Attributswerte eines Benutzers. Nur angegebene Attribute werden aktualisieren, die restlichen Attribute
     * werden ignoriert.
     */
    #[OA\Patch(operationId: 'api_users_update_attributes', tags: ['Benutzer'])]
    #[OA\RequestBody(content: new Model(type: UserAttributeRequest::class))]
    #[OA\Response(response: '204', description: 'Attributwerte wurden aktualisiert.')]
    #[OA\Response(response: '400', description: 'Validierung fehlgeschlagen.', content: new Model(type:ViolationListResponse::class))]
    #[OA\Response(response: '404', description: 'Benutzer wurde nicht gefunden.')]
    #[OA\Response(response: '500', description: 'Serverfehler', content: new Model(type: ErrorResponse::class))]
    #[Route(path: '/api/user/{uuidOrExternalId}/attributes', methods: ['PATCH'])]
    public function updateAttributes(string $uuidOrExternalId, #[MapRequestPayload] UserAttributeRequest $request): Response {
        $user = $this->getUserOrThrowNotFound($uuidOrExternalId);
        $this->attributePersister->persistUserAttributes($request->getAttributes(), $user);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
    /**
     * Removes an existing user.
     */
    #[OA\Delete(operationId: 'api_user_delete', tags: [ 'Benutzer'])]
    #[OA\Response(response: '204', description: 'Benutzer wurde erfolgreich gelöscht.')]
    #[OA\Response(response: '404', description: 'Benutzer wurde nicht gefunden.')]
    #[OA\Response(response: '500', description: 'Serverfehler', content: new Model(type: ErrorResponse::class))]
    #[Route(path: '/api/user/{uuidOrExternalId}', methods: ['DELETE'])]
    public function remove(string $uuidOrExternalId): Response {
        $user = $this->getUserOrThrowNotFound($uuidOrExternalId);
        $this->userRepository->remove($user);
        return new Response(null, Response::HTTP_NO_CONTENT);
    }
    /**
     * @throws Exception
     */
    private function transformRequest(UserRequest $request, ?User $user = null): User {
        $type = $this->userTypeRepository->findOneByUuid($request->getType());

        if(!$type instanceof UserType) {
            throw new Exception('User type not found.');
        }

        if(!$user instanceof User) {
            $user = (new User());

            if(!in_array($request->getPassword(), [null, '', '0'], true)) {
                $user->setPassword($request->getPassword());
                $user->setIsProvisioned(false);
            }
        }

        if(!$user instanceof ActiveDirectoryUser) {
            $user
                ->setFirstname($request->getFirstname())
                ->setLastname($request->getLastname())
                ->setEmail($request->getEmail())
                ->setExternalId($request->getExternalId())
                ->setEnabledFrom($request->getEnabledFrom())
                ->setEnabledUntil($request->getEnabledUntil())
                ->setIsActive($request->isActive());
        }

        $user
            ->setUsername($request->getUsername())
            ->setType($type)
            ->setGrade($request->getGrade());

        return $user;
    }
    private function getUserOrThrowNotFound(string $uuidOrExternalId): User {
        if(Uuid::isValid($uuidOrExternalId)) {
            $user = $this->userRepository->findOneByUuid($uuidOrExternalId);

            if ($user instanceof User) {
                return $user;
            }
        }

        $user = $this->userRepository->findOneByExternalId($uuidOrExternalId);

        if(!$user instanceof User || $user->isDeleted()) {
            throw $this->createNotFoundException();
        }

        return $user;
    }
}
