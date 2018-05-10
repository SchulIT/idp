<?php

namespace App\Api\User;

use Doctrine\Common\Persistence\ObjectManager;
use JMS\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserApiEndpoint extends AbstractApiEndpoint {

    private $om;

    public function __construct(ObjectManager $manager, SerializerInterface $serialiser, ValidatorInterface $validator, LoggerInterface $logger = null) {
        parent::__construct($serialiser, $validator, $logger);

        $this->om = $manager;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request): Response {

    }
}