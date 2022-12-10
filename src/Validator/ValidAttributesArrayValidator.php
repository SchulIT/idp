<?php

namespace App\Validator;

use App\Entity\ServiceAttribute;
use App\Entity\ServiceAttributeType;
use App\Repository\ServiceAttributeRepositoryInterface;
use App\Utils\ArrayUtils;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ValidAttributesArrayValidator extends ConstraintValidator {

    public function __construct(private ServiceAttributeRepositoryInterface $attributeRepository)
    {
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint) {
        if(!$constraint instanceof ValidAttributesArray) {
            throw new UnexpectedTypeException($constraint, ValidAttributesArray::class);
        }

        /** @var ServiceAttribute[] $attributes */
        $attributes = ArrayUtils::createArrayWithKeys(
            $this->attributeRepository->findAll(),
            fn(ServiceAttribute $attribute) => $attribute->getName());
        $validAttributeNames = array_keys($attributes);

        foreach($value as $attributeName => $attributeValue) {
            if(!in_array($attributeName, $validAttributeNames)) {
                $this->context
                    ->buildViolation($constraint->messageNotFound)
                    ->setParameter('{{ name }}', $attributeName)
                    ->addViolation();
                continue;
            }

            $attribute = $attributes[$attributeName];

            if($attribute->getType() === ServiceAttributeType::Text && $attributeValue !== null &&  !is_scalar($attributeValue)) {
                $this->context
                    ->buildViolation($constraint->messageInvalidValue)
                    ->setParameter('{{ name }}', $attributeName)
                    ->setParameter('{{ type }}', 'string')
                    ->setParameter('{{ given }}', gettype($attributeValue))
                    ->addViolation();
            } else if($attribute->getType() === ServiceAttributeType::Select) {
                if(!is_array($attributeValue)) {
                    $this->context
                        ->buildViolation($constraint->messageInvalidValue)
                        ->setParameter('{{ name }}', $attributeName)
                        ->setParameter('{{ type }}', 'array')
                        ->setParameter('{{ given }}', gettype($attributeValue))
                        ->addViolation();
                } else {
                    $i = 0;
                    $validValues = array_keys($attribute->getOptions());

                    foreach($attributeValue as $arrayValue) {
                        if(!in_array($arrayValue, $validValues)) {
                            $this->context
                                ->buildViolation($constraint->messageInvalidArrayItem)
                                ->setParameter('{{ name }}', $attributeName)
                                ->setParameter('{{ valid }}', json_encode($validValues, JSON_THROW_ON_ERROR))
                                ->setParameter('{{ given }}', $arrayValue)
                                ->setParameter('{{ pos }}', (string)$i)
                                ->addViolation();
                        }

                        $i++;
                    }
                }
            }
        }
    }

}