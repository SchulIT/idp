<?php

namespace App\Import;

use JMS\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractImporter implements ImporterInterface {

    protected $logger;
    protected $serializer;
    protected $validator;

    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator, LoggerInterface $logger = null) {
        $this->logger = $logger ?? new NullLogger();
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    public abstract function import($json);

    /**
     * @return LoggerInterface
     */
    protected function getLogger() {
        return $this->logger;
    }

    /**
     * @param string $json
     * @param string $className
     * @return mixed
     */
    protected function parseJson($json, $className) {
        $result = $this->serializer->deserialize($json, $className, 'json');
        return $result;
    }

    /**
     * @param $objectToValidate
     */
    protected function throwIfNotValid($objectToValidate) {
        $objects = [ ];

        if(!is_array($objectToValidate)) {
            $objects[] = $objectToValidate;
        } else {
            $objects = $objectToValidate;
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