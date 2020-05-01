<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\UserRepositoryInterface;
use App\Repository\UserTypeRepositoryInterface;
use App\Request\UserAttributeRequest;
use App\Request\UserRequest;
use App\Response\ListUserResponse;
use App\Response\Violation;
use App\Response\ViolationListResponse;
use App\Service\AttributePersister;
use Exception;
use JMS\Serializer\Exception\ValidationFailedException;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Response\ErrorResponse;

/**
 * @Route("/api/user")
 */
class UserController extends AbstractApiController {

    private $validator;
    private $userRepository;
    private $userTypeRepository;
    private $attributePersister;

    public function __construct(ValidatorInterface $validator, UserRepositoryInterface $userRepository, UserTypeRepositoryInterface $userTypeRepository,
                                AttributePersister $attributePersister, SerializerInterface $serializer) {
        parent::__construct($serializer);

        $this->validator = $validator;
        $this->userRepository = $userRepository;
        $this->userTypeRepository = $userTypeRepository;
        $this->attributePersister = $attributePersister;
    }

    /**
     * Returns a list of users.
     *
     * @Route("", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Returns a list of users",
     *     @Model(type=ListUserResponse::class)
     * )
     * @SWG\Parameter(
     *     name="offset",
     *     required=false,
     *     in="query",
     *     type="integer",
     *     description="For paginated results: specifies the position of the first user to return."
     * )
     * @SWG\Parameter(
     *     name="limit",
     *     required=false,
     *     in="query",
     *     type="integer",
     *     description="For paginated results: specifies the number of users to return."
     * )
     */
    public function list(Request $request, UserRepositoryInterface $userRepository) {
        $offset = $request->query->get('offset');
        $limit = $request->query->get('limit');

        if($offset === null || !is_numeric($offset) || $offset < 0) {
            $offset = 0;
        }

        if($limit !== null && (!is_numeric($limit) || $limit < 0)) {
            $limit = null;
        }

        $uuids = $userRepository->findAllUuids($offset, $limit);
        return $this->returnJson(new ListUserResponse($uuids));
    }

    /**
     * Returns a single user.
     *
     * @Route("/{uuid}", methods={"GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns a single user.",
     *     @Model(type=User::class)
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Empty HTTP 404 response in case the user was not found."
     * )
     * @SWG\Parameter(
     *     name="uuid",
     *     required=true,
     *     in="path",
     *     type="string",
     *     description="UUID of the user which should be returned."
     * )
     */
    public function user(User $user) {
        return $this->returnJson($user);
    }

    /**
     * Creates a new user.
     *
     * @Route("/add", methods={"POST"})
     *
     * @SWG\Parameter(
     *     name="payload",
     *     in="body",
     *     @Model(type=UserRequest::class)
     * )
     * @SWG\Response(
     *     response=201,
     *     description="User was created successfully."
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Validation failed.",
     *     @Model(type=ViolationListResponse::class)
     * )
     * @SWG\Response(
     *     response=500,
     *     description="Server error.",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    public function add(UserRequest $request) {
        $violations = [ ];
        $existingUser = $this->userRepository->findOneByUsername($request->getUsername());

        if($existingUser !== null) {
            $violations[] = new Violation('username', 'Username already in use.');
        }

        $existingUser = $this->userRepository->findOneByEmail($request->getEmail());

        if($existingUser !== null) {
            $violations[] = new Violation('email', 'Email address already in use.');
        }

        if(count($violations) > 0) {
            return $this->returnJson(
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
     * Updates an existing user. Note: property username cannot be updated using this endpoint (despite the property exists in the request).
     *
     * @Route("/{uuid}", methods={"PATCH"})
     *
     * @SWG\Parameter(
     *     name="uuid",
     *     in="path",
     *     type="string"
     * )
     * @SWG\Parameter(
     *     name="payload",
     *     in="body",
     *     @Model(type=UserRequest::class)
     * )
     * @SWG\Response(
     *     response=204,
     *     description="User was updated successfully."
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Validation failed.",
     *     @Model(type=ViolationListResponse::class)
     * )
     * @SWG\Response(
     *     response=404,
     *     description="User was not found."
     * )
     * @SWG\Response(
     *     response=500,
     *     description="Server error.",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    public function update(User $user, UserRequest $request) {
        $user = $this->transformRequest($request, $user);
        $violations = $this->validator->validate($user);

        if(count($violations) > 0) {
            throw new ValidationFailedException($violations);
        }

        $this->userRepository->persist($user);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Updates a users attributes. Notice: only given attributes are updated.
     *
     * @Route("/{uuid}/attributes", methods={"PATCH"})
     *
     * @SWG\Parameter(
     *     name="uuid",
     *     in="path",
     *     type="string"
     * )
     * @SWG\Parameter(
     *     name="payload",
     *     in="body",
     *     @Model(type=UserAttributeRequest::class)
     * )
     * @SWG\Response(
     *     response=204,
     *     description="User was successfully removed."
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Validation failed.",
     *     @Model(type=ViolationListResponse::class)
     * )
     * @SWG\Response(
     *     response=404,
     *     description="User was not found."
     * )
     * @SWG\Response(
     *     response=500,
     *     description="Server error.",
     *     @Model(type=App\Response\ErrorResponse::class)
     * )
     */
    public function updateAttributes(User $user, UserAttributeRequest $request) {
        $this->attributePersister->persistUserAttributes($request->getAttributes(), $user);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Removes an existing user.
     *
     * @Route("/{uuid}", methods={"DELETE"})
     *
     * @SWG\Parameter(
     *     name="uuid",
     *     in="path",
     *     type="string"
     * )
     * @SWG\Response(
     *     response=204,
     *     description="User was removed successfully."
     * )
     * @SWG\Response(
     *     response=404,
     *     description="User was not found."
     * )
     * @SWG\Response(
     *     response=500,
     *     description="Server error.",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    public function remove(User $user) {
        $this->userRepository->remove($user);
        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param UserRequest $request
     * @param User|null $user
     * @return User
     * @throws Exception
     */
    private function transformRequest(UserRequest $request, ?User $user = null): User {
        $type = $this->userTypeRepository->findOneByUuid($request->getType());

        if($type === null) {
            throw new Exception('User type not found.');
        }

        if($user === null) {
            $user = (new User())
                ->setUsername($request->getUsername());
        }

        $user
            ->setType($type)
            ->setGrade($request->getGrade())
            ->setEnabledFrom($request->getEnabledFrom())
            ->setEnabledUntil($request->getEnabledUntil())
            ->setInternalId($request->getInternalId())
            ->setEmail($request->getEmail())
            ->setFirstname($request->getFirstname())
            ->setLastname($request->getLastname())
            ->setIsActive($request->isActive());

        return $user;
    }
}