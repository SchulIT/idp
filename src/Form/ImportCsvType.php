<?php

namespace App\Form;

use App\Entity\UserType as UserTypeEntity;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotNull;

class ImportCsvType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('file', FileType::class, [
                'label' => 'label.file'
            ])
            ->add('delimiter', ChoiceType::class, [
                'label' => 'label.delimiter',
                'choices' => [
                    ';' => ';',
                    ',' => ','
                ],
                'constraints' => [
                    new Choice(['choices' => [';', ',', '\t']])
                ]
            ])
            ->add('userType', EntityType::class, [
                'class' => UserTypeEntity::class,
                'query_builder' => fn(EntityRepository $repository) => $repository->createQueryBuilder('t')
                    ->orderBy('t.name', 'asc'),
                'choice_label' => 'name',
                'label' => 'label.user_type',
                'expanded' => true,
                'label_attr' => [
                    'class' => 'radio-custom'
                ],
                'constraints' => [
                    new NotNull()
                ]
            ]);
    }
}