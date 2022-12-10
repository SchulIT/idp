<?php

namespace App\Form;

use App\Entity\ServiceProvider;
use App\Service\AttributeResolver;
use Doctrine\ORM\EntityRepository;
use SchulIT\CommonBundle\Form\FieldsetType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class UserRoleType extends AbstractType {
    use AttributeDataTrait;

    public function __construct(private AttributeResolver $userAttributeResolver)
    {
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
                        ->add('priority', IntegerType::class, [
                            'label' => 'label.priority.label',
                            'help' => 'label.priority.help'
                        ]);
                }
            ])
            ->add('group_attributes', AttributesType::class, [
                'legend' => 'label.attributes',
                'attribute_values' => $this->userAttributeResolver->getAttributesForRole($userRole)
            ]);
    }
}