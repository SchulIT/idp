<?php

namespace App\Controller\Api;

use App\Entity\RegistrationCode as RegistrationCodeEntity;
use App\Repository\RegistrationCodeRepositoryInterface;
use App\Repository\UserTypeRepositoryInterface;
use App\Request\RegistrationCode;
use App\Response\ErrorResponse;
use App\Response\RegistrationCodeList;
use App\Rest\ValidationFailedException;
use Exception;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/registration_code")
 */
class RegistrationCodeController extends AbstractApiController {

    private $validator;
    private $userTypeRepository;
    private $registrationCodeRepository;

    public function __construct(ValidatorInterface $validator, UserTypeRepositoryInterface $userTypeRepository, RegistrationCodeRepositoryInterface $registrationCodeRepository, SerializerInterface $serializer) {
        parent::__construct($serializer);

        $this->validator = $validator;
        $this->userTypeRepository = $userTypeRepository;
        $this->registrationCodeRepository = $registrationCodeRepository;
    }

    /**
     * Returns a list of registration codes.
     *
     * @Route("", methods={"GET"})
     * @SWG\Response(
     *     response=200,
     *     description="Returns a list of registration codes",
     *     @Model(type=RegistrationCodeList::class)
     * )
     */
    public function list() {
        return $this->returnJson(
            new RegistrationCodeList($this->registrationCodeRepository->findAllUuids())
        );
    }

    /**
     * Returns a registration code.
     *
     * @Route("/{uuid}", methods={"GET"})
     * @SWG\Parameter(
     *     name="uuid",
     *     in="path",
     *     type="string",
     *     description="UUID of the registration code"
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Returns a single registration code.",
     *     @Model(type=RegistrationCodeEntity::class)
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Registration code was not found."
     * )
     * @SWG\Response(
     *     response=500,
     *     description="Server error.",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    public function code(RegistrationCodeEntity $code) {
        return $this->returnJson($code);
    }

    /**
     * Adds a new registration code.
     *
     * @Route("", methods={"POST"})
     * @SWG\Parameter(
     *     name="payload",
     *     in="body",
     *     @Model(type=RegistrationCode::class)
     * )
     * @SWG\Response(
     *     response=201,
     *     description="Registration code was created successfully."
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
    public function add(RegistrationCode $request) {
        $code = $this->transformRequest($request);
        $violations = $this->validator->validate($code);

        if(count($violations) > 0) {
            throw new ValidationFailedException($violations);
        }

        $this->registrationCodeRepository->persist($code);

        return new Response(null, Response::HTTP_CREATED);
    }

    /**
     * Updates an existing registration code. Note: after a registration code was redeemed, the code cannot be updated anymore.
     *
     * @Route("/{uuid}", methods={"PATCH"})
     * @SWG\Parameter(
     *     name="uuid",
     *     in="path",
     *     type="string",
     *     description="UUID of the registration code"
     * )
     * @SWG\Parameter(
     *     name="payload",
     *     in="body",
     *     @Model(type="RegistrationCode::class")
     * )
     * @SWG\Response(
     *     response=204,
     *     description="Registration code was created successfully."
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Validation failed.",
     *     @Model(type=ViolationListResponse::class)
     * )
     * @SWG\Response(
     *     response=403,
     *     description="Registration code was already redeemed and thus cannot be updated."
     * )
     * @SWG\Response(
     *     response=500,
     *     description="Server error.",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    public function update(RegistrationCodeEntity $code, RegistrationCode $request) {
        if($code->getRedeemedAt() !== null) {
            return new Response(null, Response::HTTP_FORBIDDEN);
        }

        $code = $this->transformRequest($request, $code);
        $violations = $this->validator->validate($code);

        if(count($violations) > 0) {
            throw new ValidationFailedException($violations);
        }

        $this->registrationCodeRepository->persist($code);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Removes a registration code.
     *
     * @Route("/{uuid}", methods={"DELETE"})
     * @SWG\Parameter(
     *     name="uuid",
     *     in="path",
     *     type="string",
     *     description="UUID of the registration code"
     * )
     * @SWG\Response(
     *     response=204,
     *     description="Registration code was removed successfully."
     * )
     * @SWG\Response(
     *     response=500,
     *     description="Server error.",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    public function remove(RegistrationCodeEntity $code) {
        $this->registrationCodeRepository->remove($code);
        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param RegistrationCode $request
     * @param RegistrationCodeEntity|null $code
     * @return RegistrationCodeEntity
     * @throws Exception
     */
    private function transformRequest(RegistrationCode $request, ?RegistrationCodeEntity $code = null): RegistrationCodeEntity {
        $type = $this->userTypeRepository->findOneByUuid($request->getType());

        if($type === null) {
            throw new Exception('User type not found.');
        }

        if($code === null) {
            $code = (new RegistrationCodeEntity());
        }

        $code->setUsername($request->getUsername());
        $code->setCode($request->getCode());
        $code->setFirstname($request->getFirstname());
        $code->setLastname($request->getLastname());
        $code->setEmail($request->getEmail());
        $code->setGrade($request->getGrade());
        $code->setType($type);
        $code->setInternalId($request->getInternalId());
        $code->setAttributes($request->getAttributes());

        return $code;
    }
}