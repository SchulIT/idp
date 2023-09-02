<?php

namespace App\Form;

use App\Entity\ServiceAttributeType;
use App\Repository\ServiceAttributeRepositoryInterface;
use SchulIT\CommonBundle\Form\FieldsetType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AttributesType extends FieldsetType {

    public const EXPANDED_THRESHOLD = 7;

    public function __construct(private readonly ServiceAttributeRepositoryInterface $serviceAttributeRepository)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void {
        parent::configureOptions($resolver);

        $resolver
            ->setDefault('attribute_values', [ ])
            ->setDefault('mapped', false)
            ->setDefault('only_user_editable', false)
            ->remove('fields');
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $attributeValues = $options['attribute_values'];
        $onlyUserEditable = $options['only_user_editable'];

        foreach($this->serviceAttributeRepository->findAll() as $attribute) {
            $type = $attribute->getType() === ServiceAttributeType::Text ? TextType::class : ChoiceType::class;

            $options = [
                'label' => $attribute->getLabel(),
                'attr' => [
                    'help' => $attribute->getDescription()
                ],
                'required' => false,
                'mapped' => false,
                'disabled' => $attribute->isUserEditEnabled() !== true && $onlyUserEditable === true,
                'data' => $attributeValues[$attribute->getName()] ?? null
            ];

            if($type === ChoiceType::class) {
                $choices = [];
                $values = $attributeValues[$attribute->getName()] ?? [ ];

                if(!is_array($values)) {
                    $values = [ $values ];
                }

                foreach ($attribute->getOptions() as $key => $value) {
                    if($onlyUserEditable === false || in_array($key, $values)) {
                        $choices[$value] = $key;
                    }
                }

                if(count($choices) === 0) {
                    continue;
                }

                if ($attribute->isMultipleChoice()) {
                    $options['multiple'] = true;
                } else {
                    array_unshift($choices, [
                        'label.not_specified' => null
                    ]);
                    $options['placeholder'] = false;
                }

                $options['choices'] = $choices;

                if (count($choices) < static::EXPANDED_THRESHOLD) {
                    $options['expanded'] = true;
                    $options['label_attr'] = [
                        'class' => $attribute->isMultipleChoice() ? 'checkbox-custom' : 'radio-custom'
                    ];
                }
            } else if($type === TextType::class && $options['disabled']) {
                $type = ReadonlyTextType::class;
            }

            $builder
                ->add($attribute->getName(), $type, $options);
        }
    }

    public function getBlockPrefix(): string {
        return $this->getName();
    }
}