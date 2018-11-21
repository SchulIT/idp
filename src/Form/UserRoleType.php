<?php

namespace App\Form;

use App\Entity\ServiceAttribute;
use App\Entity\ServiceProvider;
use App\Repository\ServiceAttributeRepositoryInterface;
use App\Service\AttributeResolver;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Choice;

class UserRoleType extends AbstractType {
    use AttributeDataTrait;

    const EXPANDED_THRESHOLD = 7;

    private $serviceAttributeRepository;
    private $userAttributeResolver;

    public function __construct(ServiceAttributeRepositoryInterface $serviceAttributeRepository, AttributeResolver $userAttributeResolver) {
        $this->serviceAttributeRepository = $serviceAttributeRepository;
        $this->userAttributeResolver = $userAttributeResolver;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $userRole = $options['data'];

        $builder
            ->add('group_general', FieldsetType::class, [
                'legend' => 'label.general',
                'fields' => function(FormBuilderInterface $builder) {
                    $builder
                        ->add('name', TextType::class, [
                            'label' => 'label.name'
                        ])
                        ->add('description', TextType::class, [
                            'label' => 'label.description'
                        ])
                        ->add('enabledServices', EntityType::class, [
                            'class' => ServiceProvider::class,
                            'query_builder' => function(EntityRepository $repository) {
                                return $repository->createQueryBuilder('s')
                                    ->orderBy('s.name', 'asc');
                            },
                            'choice_label' => 'name',
                            'label' => 'label.services',
                            'multiple' => true,
                            'required' => false
                        ]);
                }
            ])
            ->add('group_attributes', FieldsetType::class, [
                'legend' => 'label.attributes',
                'fields' => function(FormBuilderInterface $builder) use(&$userRole) {
                    $attributeValues = $this->userAttributeResolver->getAttributesForRole($userRole);

                    foreach($this->serviceAttributeRepository->getAttributes() as $attribute) {
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

                            $choiceConstraint = new Choice();
                            $choiceConstraint->choices = $choices;
                            $choiceConstraint->multiple = true;
                            $choiceConstraint->min = 0;

                            $options['constraints'] = [ $choiceConstraint ];
                        }

                        $builder
                            ->add($attribute->getName(), $type, $options);
                    }
                }
            ]);




    }
}