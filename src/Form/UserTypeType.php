<?php

namespace App\Form;

use App\Entity\UserType;
use App\Entity\ServiceProvider;
use App\Saml\EduPersonAffliation;
use App\Service\AttributeResolver;
use Doctrine\ORM\EntityRepository;
use SchulIT\CommonBundle\Form\FieldsetType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class UserTypeType extends AbstractType {
    use AttributeDataTrait;

    public function __construct(private readonly AttributeResolver $userAttributeResolver)
    {
    }

    private function getEduPersonAffliations(): array {
        $affliations = EduPersonAffliation::getAffliations();

        $result = [ ];

        foreach($affliations as $affliation) {
            $result[$affliation] = $affliation;
        }

        return $result;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $userType = $options['data'];

        $builder
            ->add('group_general', FieldsetType::class, [
                'legend' => 'label.general',
                'fields' => function(FormBuilderInterface $builder) {
                    $builder
                        ->add('name', TextType::class, [
                            'label' => 'label.name'
                        ])
                        ->add('alias', TextType::class, [
                            'label' => 'label.alias'
                        ])
                        ->add('icon', FontAwesomeIconPicker::class, [
                            'label' => 'label.icon.label',
                            'help' => 'label.icon.help',
                            'required' => false
                        ])
                        ->add('eduPerson', ChoiceType::class, [
                            'label' => 'label.edu_person',
                            'multiple' => true,
                            'expanded' => true,
                            'choices' => $this->getEduPersonAffliations(),
                            'label_attr' => [
                                'class' => 'checkbox-custom'
                            ]
                        ])
                        ->add('enabledServices', EntityType::class, [
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
                        ])
                        ->add('canChangeName', CheckboxType::class, [
                            'required' => false,
                            'label' => 'label.can_change.name',
                            'label_attr' => [
                                'class' => 'checkbox-custom'
                            ]
                        ])
                        ->add('canChangeEmail', CheckboxType::class, [
                            'required' => false,
                            'label' => 'label.can_change.email',
                            'label_attr' => [
                                'class' => 'checkbox-custom'
                            ]
                        ])
                        ->add('canLinkStudents', CheckboxType::class, [
                            'required' => false,
                            'label' => 'label.can_link_students',
                            'label_attr' => [
                                'class' => 'checkbox-custom'
                            ]
                        ]);
                }
            ])
            ->add('group_attributes', AttributesType::class, [
                'legend' => 'label.attributes',
                'attribute_values' => $this->userAttributeResolver->getAttributesForType($userType)
            ])
            ->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $formEvent) {
                $form = $formEvent->getForm();
                $type = $formEvent->getData();

                if($type instanceof UserType && $type->isBuiltIn()) {
                    $form->get('group_general')
                        ->add('alias', TextType::class, [
                            'label' => 'label.alias',
                            'disabled' => true,
                            'data' => $type->getAlias()
                        ])
                        ->add('eduPerson', ChoiceType::class, [
                            'label' => 'label.edu_person',
                            'multiple' => true,
                            'expanded' => true,
                            'choices' => $this->getEduPersonAffliations(),
                            'label_attr' => [
                                'class' => 'checkbox-custom'
                            ],
                            'disabled' => true,
                            'data' => $type->getEduPerson()
                        ]);
                }
            });
    }
}