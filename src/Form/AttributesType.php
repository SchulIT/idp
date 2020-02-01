<?php

namespace App\Form;

use App\Entity\ServiceAttribute;
use App\Repository\ServiceAttributeRepositoryInterface;
use SchoolIT\CommonBundle\Form\FieldsetType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AttributesType extends FieldsetType {

    const EXPANDED_THRESHOLD = 7;

    private $serviceAttributeRepository;

    public function __construct(ServiceAttributeRepositoryInterface $serviceAttributeRepository) {
        $this->serviceAttributeRepository = $serviceAttributeRepository;
    }

    public function configureOptions(OptionsResolver $resolver) {
        parent::configureOptions($resolver);

        $resolver
            ->setDefault('attribute_values', [ ])
            ->setDefault('mapped', false)
            ->setDefault('only_user_editable', false)
            ->remove('fields');
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $attributeValues = $options['attribute_values'];

        foreach($this->serviceAttributeRepository->findAll() as $attribute) {
            if($attribute->isUserEditEnabled() !== true && isset($options['only_user_editable']) && $options['only_user_editable'] === true) {
                continue;
            }

            $type = $attribute->getType() === ServiceAttribute::TYPE_TEXT ? TextType::class : ChoiceType::class;
            $options = [
                'label' => $attribute->getLabel(),
                'attr' => [
                    'help' => $attribute->getDescription()
                ],
                'required' => false,
                'mapped' => false,
                'data' => $attributeValues[$attribute->getName()] ?? null
            ];

            if($type === ChoiceType::class) {
                $choices = [ ];

                foreach($attribute->getOptions() as $key => $value) {
                    $choices[$value] = $key;
                }

                if($attribute->isMultipleChoice()) {
                    $options['multiple'] = true;
                } else {
                    array_unshift($choices, [
                        'label.not_specified' => null
                    ]);
                    $options['placeholder'] = false;
                }

                $options['choices'] = $choices;

                if(count($choices) < static::EXPANDED_THRESHOLD) {
                    $options['expanded'] = true;
                }
            }

            $builder
                ->add($attribute->getName(), $type, $options);
        }
    }

    public function getBlockPrefix() {
        return $this->getName();
    }
}