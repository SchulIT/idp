<?php

namespace App\Validator;

use App\Entity\ServiceAttribute;
use App\Repository\ServiceAttributeRepositoryInterface;
use App\Utils\ArrayUtils;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ValidAttributesArrayValidator extends ConstraintValidator {

    private $attributeRepository;

    public function __construct(ServiceAttributeRepositoryInterface $attributeRepository) {
        $this->attributeRepository = $attributeRepository;
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
            function(ServiceAttribute $attribute) {
                return $attribute->getName();
            });
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

            if($attribute->getType() === ServiceAttribute::TYPE_TEXT && $attributeValue !== null &&  !is_scalar($attributeValue)) {
                $this->context
                    ->buildViolation($constraint->messageInvalidValue)
                    ->setParameter('{{ name }}', $attributeName)
                    ->setParameter('{{ type }}', 'string')
                    ->setParameter('{{ given }}', gettype($attributeValue))
                    ->addViolation();
            } else if($attribute->getType() === ServiceAttribute::TYPE_SELECT) {
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
                                ->setParameter('{{ valid }}', json_encode($validValues))
                                ->setParameter('{{ given }}', $arrayValue)
                                ->setParameter('{{ pos }}', $i)
                                ->addViolation();
                        }

                        $i++;
                    }
                }
            }
        }
    }

}