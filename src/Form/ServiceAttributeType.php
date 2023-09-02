<?php

namespace App\Form;

use App\Entity\ServiceAttributeType as ServiceAttributeTypeEnum;
use App\Entity\ServiceProvider;
use Doctrine\ORM\EntityRepository;
use SchulIT\CommonBundle\Form\FieldsetType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Contracts\Translation\TranslatorInterface;

class ServiceAttributeType extends AbstractType {

    public function __construct(private readonly TranslatorInterface $translator) { }

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder->add('group_general', FieldsetType::class, [
            'legend' => 'label.general',
            'fields' => function(FormBuilderInterface $builder) {
                $builder
                    ->add('name', TextType::class, [
                        'label' => 'label.name'
                    ])
                    ->add('label', TextType::class, [
                        'label' => 'label.label'
                    ])
                    ->add('description', TextType::class, [
                        'label' => 'label.description'
                    ])
                    ->add('isUserEditEnabled', CheckboxType::class, [
                        'label' => 'label.user_edit_enabled',
                        'required' => false,
                        'label_attr' => [
                            'class' => 'checkbox-custom'
                        ]
                    ])
                    ->add('samlAttributeName', TextType::class, [
                        'label' => 'label.saml_attribute_name'
                    ])
                    ->add('type', EnumType::class, [
                        'class' => ServiceAttributeTypeEnum::class,
                        'label' => 'label.type',
                        'label_attr' => [
                            'class' => 'radio-custom'
                        ],
                        'expanded' => true,
                        'choice_label' => fn(ServiceAttributeTypeEnum $type) => $this->translator->trans('service_attribute_type.' . $type->value, [ ], 'enums')
                    ])
                    ->add('services', EntityType::class, [
                        'class' => ServiceProvider::class,
                        'query_builder' => fn(EntityRepository $repository) => $repository->createQueryBuilder('s')
                            ->orderBy('s.name', 'asc'),
                        'choice_label' => 'name',
                        'label' => 'label.services',
                        'multiple' => true,
                        'required' => false,
                        'expanded' => true,
                        'label_attr' => [
                            'class' => 'checkbox-custom'
                        ]
                    ]);
            }
        ])
            ->add('group_select', FieldsetType::class, [
                'attr' => [
                    'id'=> 'group_select'
                ],
                'legend' => 'label.options',
                'fields' => function(FormBuilderInterface $builder) {
                    $builder
                        ->add('isMultipleChoice', CheckboxType::class, [
                            'label' => 'label.is_multiple_choice',
                            'required' => false,
                            'label_attr' => [
                                'class' => 'checkbox-custom'
                            ]
                        ])
                        ->add('options', KeyValueType::class, [
                            'value_type' => TextType::class,
                            'value_options' => [
                                'label' => 'label.value'
                            ],
                            'key_type' => TextType::class,
                            'key_options' => [
                                'label' => 'label.key'
                            ],
                            'allow_add' => true,
                            'allow_delete' => true,
                            'label' => 'label.options'
                        ]);
                }
        ])
            ->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event) {
                $attribute = $event->getData();
                $form = $event->getForm();

                if($attribute->getId() !== null) {
                    $form->get('group_general')
                        ->add('type', EnumType::class, [
                            'class' => ServiceAttributeTypeEnum::class,
                            'label' => 'label.type',
                            'label_attr' => [
                                'class' => 'radio-custom'
                            ],
                            'expanded' => true,
                            'disabled' => true,
                            'mapped' => false,
                            'data' => $attribute->getType(),
                            'choice_label' => fn(ServiceAttributeTypeEnum $type) => $this->translator->trans('service_attribute_type.' . $type->value, [ ], 'enums')
                        ]);
                }
            });
    }
}