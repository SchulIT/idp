<?php

namespace App\Form;

use App\Entity\ServiceProvider;
use Burgov\Bundle\KeyValueFormBundle\Form\Type\KeyValueType;
use Doctrine\ORM\EntityRepository;
use SchoolIT\CommonBundle\Form\FieldsetType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ServiceAttributeType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
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
                        'required' => false
                    ])
                    ->add('samlAttributeName', TextType::class, [
                        'label' => 'label.saml_attribute_name'
                    ])
                    ->add('type', ChoiceType::class, [
                        'choices' => [
                            'service_attributes.types.text' => 'text',
                            'service_attributes.types.select' => 'select'
                        ],
                        'label' => 'label.type'
                    ])
                    ->add('services', EntityType::class, [
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
            ->add('group_select', FieldsetType::class, [
                'attr' => [
                    'id'=> 'group_select'
                ],
                'legend' => 'label.options',
                'fields' => function(FormBuilderInterface $builder) {
                    $builder
                        ->add('isMultipleChoice', CheckboxType::class, [
                            'label' => 'label.is_multiple_choice',
                            'required' => false
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
                        ->add('type', ChoiceType::class, [
                            'choices' => [
                                'service_attributes.types.text' => 'text',
                                'service_attributes.types.select' => 'select'
                            ],
                            'label' => 'label.type',
                            'disabled' => true,
                            'data' => $attribute->getType()
                        ]);
                }
            });
    }
}