<?php

namespace App\Form;

use App\Entity\ServiceAttribute;
use App\Entity\ServiceProvider;
use App\Repository\ServiceAttributeRepositoryInterface;
use App\Saml\EduPersonAffliation;
use App\Service\AttributeResolver;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Choice;

class UserTypeType extends AbstractType {
    use AttributeDataTrait;

    const EXPANDED_THRESHOLD = 7;

    private $serviceAttributeRepository;
    private $userAttributeResolver;

    public function __construct(ServiceAttributeRepositoryInterface $serviceAttributeRepository, AttributeResolver $userAttributeResolver) {
        $this->serviceAttributeRepository = $serviceAttributeRepository;
        $this->userAttributeResolver = $userAttributeResolver;
    }

    private function getEduPersonAffliations() {
        $affliations = EduPersonAffliation::getAffliations();

        $result = [ ];

        foreach($affliations as $affliation) {
            $result[$affliation] = $affliation;
        }

        return $result;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
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
                        ->add('eduPerson', ChoiceType::class, [
                            'label' => 'label.edu_person',
                            'multiple' => true,
                            'expanded' => true,
                            'choices' => $this->getEduPersonAffliations()
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
                        ])
                        ->add('canChangeName', CheckboxType::class, [
                            'required' => false,
                            'label' => 'label.can_change.name'
                        ])
                        ->add('canChangeEmail', CheckboxType::class, [
                            'required' => false,
                            'label' => 'label.can_change.email'
                        ]);
                }
            ])
            ->add('group_attributes', FieldsetType::class, [
                'legend' => 'label.attributes',
                'fields' => function(FormBuilderInterface $builder) use(&$userType) {
                    $attributeValues = $this->userAttributeResolver->getAttributesForType($userType);

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

                            $options['choices'] = $choices;

                            if($attribute->isMultipleChoice()) {
                                $options['multiple'] = true;

                                if(count($choices) < static::EXPANDED_THRESHOLD) {
                                    $options['expanded'] = true;
                                }
                            }
                        }

                        $builder
                            ->add($attribute->getName(), $type, $options);
                    }
                }
            ]);
    }
}