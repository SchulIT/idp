<?php

namespace App\Api;

use JMS\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractApiEndpoint {

    protected $logger;
    protected $serialiser;
    protected $validator;

    public function __construct(SerializerInterface $serialiser, ValidatorInterface $validator, LoggerInterface $logger = null) {
        $this->logger = $logger ?? new NullLogger();
        $this->serialiser = $serialiser;
        $this->validator = $validator;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public abstract function handle(Request $request): Response;

    /**
     * @return LoggerInterface
     */
    protected function getLogger(): LoggerInterface {
        return $this->logger;
    }

    /**
     * @param string $json
     * @param string $className
     * @return mixed
     */
    protected function parseJson($json, $className) {
        $result = $this->serialiser->deserialize($json, $className, 'json');
        return $result;
    }

    /**
     * @param $object
     */
    protected function throwIfNotValid($object) {
        $objects = [ ];

        if(!is_array($object)) {
            $objects[] = $object;
        } else {
            $objects = $object;
        }

        foreach($objects as $object) {
            $errors = $this->validator->validate($object);

            if ($errors->count() > 0) {
                // Only show first error
                $error = $errors->get(0);

                throw new ValidatorException(sprintf('Invalid parameters submitted for item of type "%s" on property "%s": %s', get_class($object), $error->getPropertyPath(), $error->getMessage()));
            }
        }
    }
}