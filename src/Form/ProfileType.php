<?php

namespace App\Form;

use App\Entity\ActiveDirectoryUser;
use App\Entity\ServiceAttribute;
use App\Entity\User;
use App\Repository\ServiceAttributeRepositoryInterface;
use App\Service\AttributeResolver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\Choice;

class ProfileType extends AbstractType {
    use AttributeDataTrait;

    const EXPANDED_THRESHOLD = 7;

    private $serviceAttributeRepository;
    private $userAttributeResolver;
    private $translator;

    public function __construct(ServiceAttributeRepositoryInterface $serviceAttributeRepository, AttributeResolver $userAttributeResolver, TranslatorInterface $translator) {
        $this->serviceAttributeRepository = $serviceAttributeRepository;
        $this->userAttributeResolver = $userAttributeResolver;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        /** @var User $user */
        $user = $options['data'];

        $builder
            ->add('group_general', FieldsetType::class, [
                'legend' => 'label.general',
                'fields' => function(FormBuilderInterface $builder) use (&$user) {
                    $builder
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
                        ->add('email', EmailType::class, [
                            'label' => 'label.email'
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

                    foreach($this->serviceAttributeRepository->getAttributes() as $attribute) {
                        if($attribute->isUserEditEnabled() !== true) {
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

                            $options['choices'] = $choices;

                            if($attribute->isMultipleChoice()) {
                                $options['multiple'] = true;

                                if(count($choices) < static::EXPANDED_THRESHOLD) {
                                    $options['expanded'] = true;
                                }
                            }

                            $options['constraints'] = [
                                new Choice(array_values($choices))
                            ];
                        }

                        $builder
                            ->add($attribute->getName(), $type, $options);
                    }
                }
            ]);

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
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
                    if($user->getType()->canChangeName() !== true) {
                        $form->get('group_general')
                            ->add('firstname', TextType::class, [
                                'label' => 'label.firstname',
                                'disabled' => true
                            ])
                            ->add('lastname', TextType::class, [
                                'label' => 'label.lastname',
                                'disabled' => true,
                                'attr' => [
                                    'help' => $this->translator->trans('label.can_change.name_hint')
                                ]
                            ]);
                    }

                    if($user->getType()->canChangeEmail() !== true) {
                        $form->get('group_general')
                            ->add('email', EmailType::class, [
                                'label' => 'label.email',
                                'disabled' => true,
                                'attr' => [
                                    'help' => $this->translator->trans('label.can_change.email_hint')
                                ]
                            ]);
                    }

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
                            'required' => false,
                            'first_options'  => ['label' => 'label.password'],
                            'second_options' => ['label' => 'label.repeat_password']
                        ]);
                }

                if($user instanceof ActiveDirectoryUser) {
                    $form->remove('group_password');
                    $form->get('group_general')
                        ->add('username', TextType::class, [
                            'disabled' => true,
                            'label' => 'label.username',
                            'attr' => [ 'help' => 'info.attribute_must_changed_in_ad' ]
                        ])
                        ->add('firstname', TextType::class, [
                            'disabled' => true,
                            'label' => 'label.firstname',
                            'attr' => [ 'help' => 'info.attribute_must_changed_in_ad' ]
                        ])
                        ->add('lastname', TextType::class, [
                            'disabled' => true,
                            'label' => 'label.lastname',
                            'attr' => [ 'help' => 'info.attribute_must_changed_in_ad' ]
                        ]);
                }
            });
    }
}