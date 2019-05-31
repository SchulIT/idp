<?php

namespace App\Form;

use App\Entity\ActiveDirectoryUser;
use App\Entity\ServiceAttribute;
use App\Entity\ServiceProvider;
use App\Entity\User;
use App\Entity\UserRole;
use App\Entity\UserType as UserTypeEntity;
use App\Repository\ServiceAttributeRepositoryInterface;
use App\Security\PasswordStrengthHelper;
use App\Service\AttributeResolver;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;

class UserType extends AbstractType {
    use AttributeDataTrait;
    
    const EXPANDED_THRESHOLD = 7;

    private $serviceAttributeRepository;
    private $userAttributeResolver;
    private $passwordStrengthHelper;

    public function __construct(ServiceAttributeRepositoryInterface $serviceAttributeRepository, AttributeResolver $userAttributeResolver, PasswordStrengthHelper $passwordStrengthHelper) {
        $this->serviceAttributeRepository = $serviceAttributeRepository;
        $this->userAttributeResolver = $userAttributeResolver;
        $this->passwordStrengthHelper = $passwordStrengthHelper;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $user = $options['data'];

        $builder
            ->add('group_general', FieldsetType::class, [
                'legend' => 'label.general',
                'fields' => function(FormBuilderInterface $builder) {
                    $builder
                        ->add('id', TextType::class, [
                            'disabled' => true,
                            'label' => 'label.id'
                        ])
                        ->add('internalId', TextType::class, [
                            'disabled' => true,
                            'label' => 'label.internal_id'
                        ])
                        ->add('samAccountName', TextType::class, [
                            'disabled' => true,
                            'label' => 'label.samAccountName'
                        ])
                        ->add('username', TextType::class, [
                            'disabled' => true,
                            'label' => 'label.username'
                        ])
                        ->add('firstname', TextType::class, [
                            'label' => 'label.firstname'
                        ])
                        ->add('lastname', TextType::class, [
                            'label' => 'label.lastname'
                        ])
                        ->add('isActive', CheckboxType::class, [
                            'label' => 'label.is_active',
                            'required' => false
                        ])
                        ->add('enabledFrom', DateType::class, [
                            'label' => 'label.enabled_from',
                        ])
                        ->add('enabledUntil', DateType::class, [
                            'label' => 'label.enabled_until'
                        ])
                        ->add('email', EmailType::class, [
                            'label' => 'label.email'
                        ])
                        ->add('grade', TextType::class, [
                            'label' => 'label.grade',
                            'required' => false
                        ])
                        ->add('type', EntityType::class, [
                            'class' => UserTypeEntity::class,
                            'query_builder' => function(EntityRepository $repository) {
                                return $repository->createQueryBuilder('t')
                                    ->orderBy('t.name', 'asc');
                            },
                            'choice_label' => 'name',
                            'label' => 'label.user_type'
                        ])
                        ->add('userRoles', EntityType::class, [
                            'class' => UserRole::class,
                            'query_builder' => function(EntityRepository $repository) {
                                return $repository->createQueryBuilder('r')
                                    ->orderBy('r.name', 'asc');
                            },
                            'choice_label' => 'name',
                            'label' => 'label.user_roles',
                            'multiple' => true,
                            'required' => false
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
            ->add('group_idp', FieldsetType::class, [
                'legend' => 'label.idp',
                'fields' => function(FormBuilderInterface $builder) {
                    $builder
                        ->add('roles', ChoiceType::class, [
                            'choices' => [
                                'idp.roles.user' => 'ROLE_UESR',
                                'idp.roles.admin' => 'ROLE_ADMIN',
                                'idp.roles.super_admin' => 'ROLE_SUPER_ADMIN'
                            ],
                            'multiple' => true,
                            'expanded' => true,
                            'label' => 'label.roles'
                        ]);
                }
            ])
            ->add('group_password', FieldsetType::class, [
                'legend' => 'label.password',
                'fields' => function(FormBuilderInterface $builder) {
                    $builder
                        ->add('password', RepeatedType::class, [
                            'mapped' => false,
                            'type' => PasswordType::class,
                            'invalid_message' => 'The password fields must match.',
                            'options' => [
                                'attr' => [
                                    'class' => 'password-field'
                                ],
                            ],
                            'constraints' => [
                                $this->passwordStrengthHelper->getConstraints(),
                            ],
                            'required' => true,
                            'first_options'  => ['label' => 'label.password'],
                            'second_options' => ['label' => 'label.repeat_password']
                        ]);
                }
            ])
            ->add('group_attributes', FieldsetType::class, [
                'legend' => 'label.attributes',
                'mapped' => false,
                'fields' => function(FormBuilderInterface $builder) use(&$user) {
                    $attributeValues = $this->userAttributeResolver->getAttributeValuesForUser($user);

                    foreach($this->serviceAttributeRepository->findAll() as $attribute) {
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
                            $choiceConstraint->multiple = $attribute->isMultipleChoice();
                            $choiceConstraint->min = 0;

                            $options['constraints'] = [ $choiceConstraint ];
                        }

                        $builder
                            ->add($attribute->getName(), $type, $options);
                    }
                }
            ]);

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
                /** @var User $user */
                $user = $event->getData();
                $form = $event->getForm();

                if($user->getId() === null) {
                    $form->get('group_general')
                        ->remove('id')
                        ->remove('internalId')
                        ->add('username', TextType::class, [
                            'label' => 'label.username'
                        ]);
                } else {
                    $form->get('group_password')
                        ->add('password', RepeatedType::class, [
                            'mapped' => false,
                            'type' => PasswordType::class,
                            'invalid_message' => 'The password fields must match.',
                            'options' => [
                                'attr' => [
                                    'class' => 'password-field'
                                ],
                            ],
                            'constraints' => $this->passwordStrengthHelper->getConstraints(),
                            'required' => false,
                            'first_options'  => ['label' => 'label.password'],
                            'second_options' => ['label' => 'label.repeat_password']
                        ]);
                }

                if(!$user instanceof ActiveDirectoryUser) {
                    $form->get('group_general')
                        ->remove('samAccountName');
                } else {
                    $form->remove('group_password');
                    $form->get('group_general')
                        ->add('username', TextType::class, [
                            'disabled' => true,
                            'label' => 'label.username',
                            'attr' => [ 'help' => 'info.attribute_must_change_in_ad' ]
                        ])
                        ->add('firstname', TextType::class, [
                            'disabled' => true,
                            'label' => 'label.firstname',
                            'attr' => [ 'help' => 'info.attribute_must_change_in_ad' ]
                        ])
                        ->add('lastname', TextType::class, [
                            'disabled' => true,
                            'label' => 'label.lastname',
                            'attr' => [ 'help' => 'info.attribute_must_change_in_ad' ]
                        ]);
                }
            });
    }
}