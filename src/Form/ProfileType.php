<?php

namespace App\Form;

use App\Entity\ActiveDirectoryUser;
use App\Entity\User;
use App\Security\PasswordStrengthHelper;
use App\Service\AttributeResolver;
use SchulIT\CommonBundle\Form\FieldsetType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProfileType extends AbstractType {
    use AttributeDataTrait;

    private $userAttributeResolver;
    private $translator;
    private $passwordStrengthHelper;

    public function __construct(AttributeResolver $userAttributeResolver, TranslatorInterface $translator, PasswordStrengthHelper $passwordStrengthHelper) {
        $this->userAttributeResolver = $userAttributeResolver;
        $this->translator = $translator;
        $this->passwordStrengthHelper = $passwordStrengthHelper;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        /** @var User $user */
        $user = $options['data'];

        $builder
            ->add('username', TextType::class, [
                'disabled' => true,
                'label' => 'label.username',
                'attr' => [
                    'autocomplete' => 'off'
                ]
            ])
            ->add('firstname', TextType::class, [
                'label' => 'label.firstname',
                'required' => false
            ])
            ->add('lastname', TextType::class, [
                'label' => 'label.lastname',
                'required' => false
            ])
            ->add('email', RepeatedType::class, [
                'mapped' => false,
                'type' => EmailType::class,
                'invalid_message' => 'The email fields must match.',
                'constraints' => new Email(),
                'required' => false,
                'first_options' => [
                    'label' => 'label.email'
                ],
                'second_options' => [
                    'label' => 'label.repeat_email'
                ]
            ])
            ->add('group_attributes', AttributesType::class, [
                'legend' => 'label.attributes',
                'mapped' => false,
                'only_user_editable' => true,
                'attribute_values' => $this->userAttributeResolver->getAttributeValuesForUser($user)
            ]);

        $builder
            ->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event) {
                $user = $event->getData();
                $form = $event->getForm();

                if($form->has('group_email')) {
                    $form->get('group_email')
                        ->get('email')
                        ->setData($user->getEmail());
                }
            })
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
                $user = $event->getData();
                $form = $event->getForm();

                if($user->getId() === null) {
                    $form->get('group_general')
                        ->remove('id')
                        ->remove('externalId')
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
                        $form->remove('group_email');
                        $form->add('email', EmailType::class, [
                                'label' => 'label.email',
                                'disabled' => true,
                                'required' => false,
                                'attr' => [
                                    'help' => $this->translator->trans('label.can_change.email_hint')
                                ]
                            ]);
                    }
                }

                if($user instanceof ActiveDirectoryUser) {
                    $form->remove('group_password');
                    $form->remove('group_email');
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
                        ])
                        ->add('email', EmailType::class, [
                            'disabled' => true,
                            'label' => 'label.email',
                            'attr' => [ 'help' => 'info.attribute_must_change_in_ad' ]
                        ]);
                }
            });
    }
}