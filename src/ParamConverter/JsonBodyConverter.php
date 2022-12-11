<?php

namespace App\ParamConverter;

use App\Rest\ValidationFailedException;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class JsonBodyConverter implements ParamConverterInterface {

    private const ContentType = 'json';

    private array $defaultOptions = [
        'validate' => true,
        'version' => null,
        'groups' => null
    ];

    public function __construct(private readonly string $prefix, private readonly SerializerInterface $serializer, private readonly ValidatorInterface $validator)
    {
    }

    /**
     * @inheritDoc
     * @throws BadRequestHttpException
     */
    public function apply(Request $request, ParamConverter $configuration): bool {
        $contentType = $request->getContentType();

        if($contentType !== self::ContentType) {
            throw new BadRequestHttpException(sprintf('Request header "Content-Type" must be "application/json", "%s" provided.', $contentType));
        }

        $name = $configuration->getName();
        $class = $configuration->getClass();
        $json = $request->getContent();

        $options = $this->getOptions($configuration);

        try {
            $object = $this->serializer->deserialize($json, $class, 'json');

            if($options['validate'] === true) {
                $validations = $this->validator->validate($object);

                if($validations->count() > 0) {
                    throw new ValidationFailedException($validations);
                }
            }

            $request->attributes->set($name, $object);
        } catch (Exception) {
            throw new BadRequestHttpException('Request body does not contain valid JSON.');
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function supports(ParamConverter $configuration): bool {
        $class = $configuration->getClass();

        if(str_starts_with($class, $this->prefix)) {
            return true;
        }

        return false;
    }

    private function getOptions(ParamConverter $configuration): array {
        return array_replace($this->defaultOptions, $configuration->getOptions());
    }

}