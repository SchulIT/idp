<?php

namespace App\Form;

use App\Entity\RegistrationCode;
use App\Entity\UserType as UserTypeEntity;
use App\Service\AttributeResolver;
use SchoolIT\CommonBundle\Form\FieldsetType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class RegistrationCodeType extends AbstractType {

    use AttributeDataTrait;

    private $userAttributeResolver;

    public function __construct(AttributeResolver $userAttributeResolver) {
        $this->userAttributeResolver = $userAttributeResolver;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $code = $options['data'];

        $builder
            ->add('group_general', FieldsetType::class, [
                'legend' => 'label.general',
                'fields' => function(FormBuilderInterface $builder) {
                    $this->addFields($builder);
                }
            ])
            ->add('group_attributes', AttributesType::class, [
                'legend' => 'label.attributes',
                'attribute_values' => $this->userAttributeResolver->getAttributesForRegistrationCode($code)
            ]);

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
                $form = $event->getForm();
                /** @var RegistrationCode|null $code */
                $code = $event->getData();

                if($code !== null && $code->wasRedeemed()) {
                    $this->addFields($form->get('group_general'), true, false);
                } else if($code !== null && $code->getId() !== null) {
                    $this->addFields($form->get('group_general'), false, false);
                }
            });
    }

    /**
     * @param FormInterface|FormBuilderInterface $builder
     * @param bool $readonly
     * @param bool $showGenerator Whether or not to display a code generator button
     */
    private function addFields($builder, bool $readonly = false, bool $showGenerator = true) {
        $codeType = $showGenerator === true ? CodeGeneratorType::class : TextType::class;

        $builder
            ->add('code', $codeType, [
                'label' => 'label.code',
                'attr' => [
                    'readonly' => $readonly
                ]
            ])
            ->add('username', TextType::class, [
                'label' => 'label.username',
                'required' => false,
                'attr' => [
                    'readonly' => $readonly
                ]
            ])
            ->add('usernameSuffix', TextType::class, [
                'label' => 'label.username_suffix',
                'required' => false,
                'attr' => [
                    'readonly' => $readonly
                ]
            ])
            ->add('firstname', TextType::class, [
                'label' => 'label.firstname',
                'required' => false,
                'help' => 'codes.add.help.must_complete_if_null',
                'attr' => [
                    'readonly' => $readonly
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => 'label.lastname',
                'required' => false,
                'help' => 'codes.add.help.must_complete_if_null',
                'attr' => [
                    'readonly' => $readonly
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'label.email',
                'required' => false,
                'help' => 'codes.add.help.must_complete_if_null',
                'attr' => [
                    'readonly' => $readonly
                ]
            ])
            ->add('populateFakePersonalData', CheckboxType::class, [
                'label' => 'label.populate_fake_personal_data.label',
                'help' => 'label.populate_fake_personal_data.help',
                'required' => false,
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ]
            ])
            ->add('grade', TextType::class, [
                'label' => 'label.grade',
                'required' => false,
                'attr' => [
                    'readonly' => $readonly
                ]
            ])
            ->add('type', EntityType::class, [
                'label' => 'label.user_type',
                'class' => UserTypeEntity::class,
                'choice_label' => function(UserTypeEntity $type) {
                    return $type->getName();
                },
                'attr' => [
                    'readonly' => $readonly
                ],
                'expanded' => true,
                'label_attr' => [
                    'class' => 'radio-custom'
                ]
            ])
            ->add('externalId', TextType::class, [
                'label' => 'label.external_id',
                'required' => false,
                'attr' => [
                    'readonly' => $readonly
                ]
            ])
        ;
    }
}