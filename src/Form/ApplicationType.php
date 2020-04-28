<?php

namespace App\Form;

use App\Entity\ServiceProvider;
use Doctrine\ORM\EntityRepository;
use FervoEnumBundle\Generated\Form\ApplicationScopeType;
use SchoolIT\CommonBundle\Form\FieldsetType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ApplicationType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('group_general', FieldsetType::class, [
                'legend' => 'label.general',
                'fields' => function(FormBuilderInterface $builder) use (&$user) {
                    $builder
                        ->add('name', TextType::class, [
                            'label' => 'label.name'
                        ])
                        ->add('scope', ApplicationScopeType::class, [
                            'label' => 'label.application_scope',
                            'expanded' => true,
                            'label_attr' => [
                                'class' => 'radio-custom'
                            ]
                        ])
                        ->add('service', EntityType::class, [
                            'class' => ServiceProvider::class,
                            'query_builder' => function(EntityRepository $repository) {
                                return $repository->createQueryBuilder('s')
                                    ->orderBy('s.name', 'asc');
                            },
                            'choice_label' => 'name',
                            'label' => 'label.service',
                            'required' => false,
                            'label_attr' => [
                                'class' => 'radio-custom'
                            ],
                            'multiple' => false,
                            'expanded' => true
                        ])
                        ->add('description', TextType::class, [
                            'label' => 'label.description'
                        ]);
                }
            ]);
    }
}