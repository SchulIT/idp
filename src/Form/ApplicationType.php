<?php

namespace App\Form;

use App\Entity\ApplicationScope;
use App\Entity\SamlServiceProvider;
use Doctrine\ORM\EntityRepository;
use SchulIT\CommonBundle\Form\FieldsetType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ApplicationType extends AbstractType {

    public function __construct(private readonly TranslatorInterface $translator) { }

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('group_general', FieldsetType::class, [
                'legend' => 'label.general',
                'fields' => function(FormBuilderInterface $builder) {
                    $builder
                        ->add('name', TextType::class, [
                            'label' => 'label.name'
                        ])
                        ->add('scope', EnumType::class, [
                            'class' => ApplicationScope::class,
                            'label' => $this->translator->trans('label.application_scope', [], 'enums'),
                            'expanded' => true,
                            'label_attr' => [
                                'class' => 'radio-custom'
                            ],
                            'choice_label' => fn(ApplicationScope $scope) => $this->translator->trans('application_scope.'.$scope->value, [], 'enums')
                        ])
                        ->add('service', EntityType::class, [
                            'class' => SamlServiceProvider::class,
                            'query_builder' => fn(EntityRepository $repository) => $repository->createQueryBuilder('s')
                                ->orderBy('s.name', 'asc'),
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