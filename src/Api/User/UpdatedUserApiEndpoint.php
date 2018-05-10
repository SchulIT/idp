<?php

namespace App\Api\User;

use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use JMS\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UpdatedUserApiEndpoint extends AbstractApiEndpoint {
    private $om;

    public function __construct(ObjectManager $manager, SerializerInterface $serialiser, ValidatorInterface $validator, LoggerInterface $logger = null) {
        parent::__construct($serialiser, $validator, $logger);

        $this->om = $manager;
    }

    public function handle(Request $request): Response {
        /** @var UpdatedUsersRequestData $requestData */
        $requestData = $this->parseJson($request->getContent(), UpdatedUsersRequestData::class);
        $this->throwIfNotValid($requestData);

        /** @var int[] $user */
        $userIds = $this->om->getRepository(User::class)
            ->getUsersUpdatedAfter($requestData->updatedAfter);

        $responseData = new UpdatedUsersResponseData();
        $responseData->userIds = $userIds;

        return new JsonResponse($responseData);
    }
}