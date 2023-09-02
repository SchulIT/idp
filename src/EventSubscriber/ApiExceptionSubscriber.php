<?php

namespace App\EventSubscriber;

use App\Response\ErrorResponse;
use App\Response\Violation;
use App\Response\ViolationListResponse;
use App\Rest\ValidationFailedException;
use JMS\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class ApiExceptionSubscriber implements EventSubscriberInterface {

    private const JsonContentType = 'application/json';

    public function __construct(private readonly SerializerInterface $serializer, private readonly LoggerInterface $logger)
    {
    }

    public function onKernelException(ExceptionEvent $event): void {
        $request = $event->getRequest();
        $acceptable = $request->getAcceptableContentTypes();

        if(!in_array(self::JsonContentType, $request->getAcceptableContentTypes())) {
            return;
        }

        $throwable = $event->getThrowable();
        $code = Response::HTTP_INTERNAL_SERVER_ERROR;

        // Case 1: general HttpException (Authorization/Authentication) or BadRequest
        if($throwable instanceof HttpException) {
            $code = $throwable->getStatusCode();
            $message = new ErrorResponse($throwable->getMessage(), $throwable::class);
        } else if($throwable instanceof ValidationFailedException) { // Case 2: validation failed
            $code = Response::HTTP_BAD_REQUEST;

            $violations = [ ];
            foreach($throwable->getConstraintViolations() as $violation) {
                $violations[] = new Violation($violation->getPropertyPath(), (string)$violation->getMessage());
            }

            $message = new ViolationListResponse($violations);
        } else { // Case 3: General error
            $message = new ErrorResponse(
                'An unknown error occured.',
                $throwable::class
            );

            $this->logger->error($throwable->getMessage(), [
                'e' => $throwable
            ]);
        }

        $validStatusCodes = array_keys(Response::$statusTexts);
        if(!in_array($code, $validStatusCodes)) {
            $code = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        $response = new Response(
            $this->serializer->serialize($message, 'json'),
            $code,
            [
                'Content-Type' => 'application/json'
            ]
        );

        $event->setResponse($response);
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array {
        return [
            KernelEvents::EXCEPTION => 'onKernelException'
        ];
    }
}