<?php

namespace App\Form;

use App\Entity\ActiveDirectoryUser;
use App\Entity\ServiceProvider;
use App\Entity\User;
use App\Entity\UserRole;
use App\Entity\UserType as UserTypeEntity;
use App\Security\PasswordStrengthHelper;
use App\Service\AttributeResolver;
use Doctrine\ORM\EntityRepository;
use SchulIT\CommonBundle\Form\FieldsetType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class UserType extends AbstractType {
    use AttributeDataTrait;

    private $userAttributeResolver;
    private $passwordStrengthHelper;

    public function __construct(AttributeResolver $userAttributeResolver, PasswordStrengthHelper $passwordStrengthHelper) {
        $this->userAttributeResolver = $userAttributeResolver;
        $this->passwordStrengthHelper = $passwordStrengthHelper;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $user = $options['data'];

        $builder
            ->add('group_general', FieldsetType::class, [
                'legend' => 'label.general',
                'fields' => function(FormBuilderInterface $builder) use($user) {
                    $builder
                        ->add('uuid', TextType::class, [
                            'disabled' => true,
                            'label' => 'label.uuid',
                            'help' => 'label.uuid_help'
                        ])
                        ->add('externalId', TextType::class, [
                            'disabled' => $user instanceof ActiveDirectoryUser,
                            'label' => 'label.external_id',
                            'help' => 'label.external_id_help',
                            'required' => false
                        ])
                        ->add('username', EmailType::class, [
                            'disabled' => true,
                            'label' => 'label.username'
                        ])
                        ->add('firstname', TextType::class, [
                            'label' => 'label.firstname',
                            'required' => false
                        ])
                        ->add('lastname', TextType::class, [
                            'label' => 'label.lastname',
                            'required' => false
                        ])
                        ->add('isActive', CheckboxType::class, [
                            'label' => 'label.is_active',
                            'required' => false,
                            'label_attr' => [
                                'class' => 'checkbox-custom'
                            ]
                        ])
                        ->add('enabledFrom', DateType::class, [
                            'label' => 'label.enabled_from',
                            'widget' => 'single_text',
                            'required' => false
                        ])
                        ->add('enabledUntil', DateType::class, [
                            'label' => 'label.enabled_until',
                            'widget' => 'single_text',
                            'required' => false
                        ])
                        ->add('email', EmailType::class, [
                            'label' => 'label.email',
                            'required' => false
                        ])
                        ->add('isEmailConfirmationPending', CheckboxType::class, [
                            'label' => 'label.email_confirmation_pending.label',
                            'help' => 'label.email_confirmation_pending.help',
                            'required' => false,
                            'label_attr' => [
                                'class' => 'checkbox-custom'
                            ]
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
                            'label' => 'label.user_type',
                            'expanded' => true,
                            'label_attr' => [
                                'class' => 'radio-custom'
                            ]
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
                            'required' => false,
                            'expanded' => true,
                            'label_attr' => [
                                'class' => 'checkbox-custom'
                            ]
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
                            'required' => false,
                            'expanded' => true,
                            'label_attr' => [
                                'class' => 'checkbox-custom'
                            ]
                        ]);
                }
            ])
            ->add('group_links', FieldsetType::class, [
                'legend' => 'label.students_simple',
                'fields' => function(FormBuilderInterface $builder) {
                    $builder
                        ->add('linkedStudents', EntityType::class, [
                            'label' => 'label.students_simple',
                            'required' => false,
                            'class' => User::class,
                            'choice_label' => function(User $user) {
                                if(!empty($user->getGrade())) {
                                    return sprintf('%s, %s (%s)', $user->getLastname(), $user->getFirstname(), $user->getGrade());
                                }

                                return sprintf('%s, %s (%s)', $user->getLastname(), $user->getFirstname(), $user->getUsername());
                            },
                            'query_builder' => function(EntityRepository $repository) {
                                return $repository->createQueryBuilder('u')
                                    ->leftJoin('u.type', 't')
                                    ->where("t.alias = 'student'")
                                    ->orderBy('u.username', 'asc');
                            },
                            'multiple' => true,
                            'by_reference' => false,
                            'attr' => [
                                'data-choice' => 'true'
                            ]
                        ]);
                }
            ])
            ->add('group_idp', FieldsetType::class, [
                'legend' => 'label.idp',
                'fields' => function(FormBuilderInterface $builder) {
                    $builder
                        ->add('roles', ChoiceType::class, [
                            'choices' => [
                                'idp.roles.user' => 'ROLE_USER',
                                'idp.roles.admin' => 'ROLE_ADMIN',
                                'idp.roles.super_admin' => 'ROLE_SUPER_ADMIN'
                            ],
                            'multiple' => true,
                            'expanded' => true,
                            'label' => 'label.roles',
                            'label_attr' => [
                                'class' => 'checkbox-custom'
                            ]
                        ]);
                }
            ])
            ->add('group_password', FieldsetType::class, [
                'legend' => 'label.password',
                'fields' => function(FormBuilderInterface $builder) {
                    $builder
                        ->add('mustChangePassword', CheckboxType::class, [
                            'label' => 'label.must_change_password',
                            'required' => false,
                            'label_attr' => [
                                'class' => 'checkbox-custom'
                            ]
                        ])
                        ->add('canChangePassword', CheckboxType::class, [
                            'label' => 'label.can_change_password',
                            'required' => false,
                            'label_attr' => [
                                'class' => 'checkbox-custom'
                            ]
                        ])
                        ->add('password', RepeatedType::class, [
                            'mapped' => false,
                            'type' => PasswordType::class,
                            'invalid_message' => 'The password fields must match.',
                            'options' => [
                                'attr' => [
                                    'class' => 'password-field',
                                    'autocomplete' => 'new-password'
                                ],
                            ],
                            'constraints' => $this->passwordStrengthHelper->getConstraints(),
                            'required' => true,
                            'first_options'  => ['label' => 'label.password'],
                            'second_options' => ['label' => 'label.repeat_password']
                        ]);
                }
            ])
            ->add('group_attributes', AttributesType::class, [
                'legend' => 'label.attributes',
                'mapped' => false,
                'attribute_values' => $this->userAttributeResolver->getAttributeValuesForUser($user)
            ]);

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
                /** @var User $user */
                $user = $event->getData();
                $form = $event->getForm();

                if($user->getId() === null) {
                    $form->get('group_general')
                        ->remove('id')
                        ->remove('externalId')
                        ->remove('isEmailConfirmationPending')
                        ->add('username', EmailType::class, [
                            'label' => 'label.username'
                        ]);
                } else {
                    if($user->isEmailConfirmationPending() !== true) {
                        $form->get('group_general')
                            ->remove('isEmailConfirmationPending');
                    }

                    $form->get('group_password')
                        ->add('password', RepeatedType::class, [
                            'mapped' => false,
                            'type' => PasswordType::class,
                            'invalid_message' => 'The password fields must match.',
                            'options' => [
                                'attr' => [
                                    'class' => 'password-field',
                                    'autocomplete' => 'new-password'
                                ],
                            ],
                            'constraints' => $this->passwordStrengthHelper->getConstraints(),
                            'required' => false,
                            'first_options'  => ['label' => 'label.password'],
                            'second_options' => ['label' => 'label.repeat_password']
                        ]);
                }

                if($user instanceof ActiveDirectoryUser) {
                    $form->remove('group_password');
                    $form->get('group_general')
                        ->remove('isEmailConfirmationPending')
                        ->add('username', EmailType::class, [
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

                if($user->getType()->isCanLinkStudents() !== true) {
                    $form->remove('group_links');
                }
            });
    }
}