<?php

namespace App\Controller\Api;

use Throwable;
use App\Entity\Application;
use App\Service\IdpExchangeService;
use JMS\Serializer\Exception\Exception;
use JMS\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;
use SchulIT\IdpExchange\Request\UpdatedUsersRequest;
use SchulIT\IdpExchange\Request\UserRequest;
use SchulIT\IdpExchange\Request\UsersRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(path: '/exchange')]
class IdpExchangeController extends AbstractApiController {

    public function __construct(private IdpExchangeService $service, SerializerInterface $serializer, private ValidatorInterface $validator, private LoggerInterface $logger) {
        parent::__construct($serializer);
    }

    /**
     * Default IdP Exchange controller (only used for testing purporses)
     */
    #[Route(path: '', name: 'idp_exchange_default')]
    public function index(): Response {
        return $this->returnJson([]);
    }

    #[Route(path: '/updated_users', name: 'idp_exchange_updated_users', methods: ['POST'])]
    public function updatedUsers(Request $request): Response {
        $json = $request->getContent();
        /** @var UpdatedUsersRequest $exchangeRequest */
        $exchangeRequest = $this->parseAndValidateRequestOrThrowError($json, UpdatedUsersRequest::class);

        $response = $this->service->getUpdatedUsers($exchangeRequest);
        return $this->returnJson($response);
    }

    #[Route(path: '/users', name: 'idp_exchange_users', methods: ['POST'])]
    public function users(Request $request): Response {
        /** @var Application $application */
        $application = $this->getUser();

        $json = $request->getContent();
        /** @var UsersRequest $exchangeRequest */
        $exchangeRequest = $this->parseAndValidateRequestOrThrowError($json, UsersRequest::class);

        $response = $this->service->getUsers($exchangeRequest, $application->getService()->getEntityId());
        return $this->returnJson($response);
    }

    #[Route(path: '/user', name: 'idp_exchange_user', methods: ['POST'])]
    public function user(Request $request): Response {
        /** @var Application $application */
        $application = $this->getUser();

        $json = $request->getContent();
        /** @var UserRequest $exchangeRequest */
        $exchangeRequest = $this->parseAndValidateRequestOrThrowError($json, UserRequest::class);

        $response = $this->service->getUser($exchangeRequest, $application->getService()->getEntityId());
        return $this->returnJson($response);
    }

    /**
     * @return object
     */
    private function parseAndValidateRequestOrThrowError(string $json, string $type) {
        try {
            $request = $this->serializer->deserialize($json, $type, 'json');

            $violations = $this->validator->validate($request);
            if($violations->count() === 0) {
                return $request;
            }

            $this->logFailedValidation($violations);
        } catch (Exception $e) {
            $this->logger->alert(sprintf('Invalid JSON body for type "%s": %s', $type, $e->getMessage()));
        } catch (Throwable $e) {
            $this->logger->alert(sprintf('Exception "%s" thrown with message "%s"', $e::class, $e->getMessage()));
        }

        throw new BadRequestHttpException();
    }

    private function logFailedValidation(ConstraintViolationListInterface $violationList): void {
        foreach($violationList as $violation) {
            $this->logger->alert(
                sprintf('Invalid request: property "%s" failed violation with message "%s"', $violation->getPropertyPath(), $violation->getMessage())
            );
        }
    }
}