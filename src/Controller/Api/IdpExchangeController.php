<?php

namespace App\Controller\Api;

use App\Entity\Application;
use App\Service\IdpExchangeService;
use Psr\Log\LoggerInterface;
use SchulIT\IdpExchange\Request\UpdatedUsersRequest;
use SchulIT\IdpExchange\Request\UserRequest;
use SchulIT\IdpExchange\Request\UsersRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(path: '/exchange')]
class IdpExchangeController extends AbstractController {

    public function __construct(private readonly IdpExchangeService $service) { }

    /**
     * Default IdP Exchange controller (only used for testing purporses)
     */
    #[Route(path: '', name: 'idp_exchange_default')]
    public function index(): Response {
        return $this->json([]);
    }

    #[Route(path: '/updated_users', name: 'idp_exchange_updated_users', methods: ['POST'])]
    public function updatedUsers(UpdatedUsersRequest $exchangeRequest): Response {
        $response = $this->service->getUpdatedUsers($exchangeRequest);
        return $this->json($response);
    }

    #[Route(path: '/users', name: 'idp_exchange_users', methods: ['POST'])]
    public function users(UsersRequest $exchangeRequest): Response {
        /** @var Application $application */
        $application = $this->getUser();

        $response = $this->service->getUsers($exchangeRequest, $application->getService()->getEntityId());
        return $this->json($response);
    }

    #[Route(path: '/user', name: 'idp_exchange_user', methods: ['POST'])]
    public function user(UserRequest $exchangeRequest): Response {
        /** @var Application $application */
        $application = $this->getUser();

        $response = $this->service->getUser($exchangeRequest, $application->getService()->getEntityId());
        return $this->json($response);
    }
}