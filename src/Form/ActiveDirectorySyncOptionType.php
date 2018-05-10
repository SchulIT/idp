<?php

namespace App\Form;

use App\Entity\ActiveDirectorySyncSourceType;
use App\Entity\UserType as UserTypeEntity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ActiveDirectorySyncOptionType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('name', TextType::class, [
                'label' => 'label.name'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'label.description'
            ])
            ->add('sourceType', ChoiceType::class, [
                'label' => 'label.source',
                'choices' => [
                    'label.ad_group' => ActiveDirectorySyncSourceType::GROUP,
                    'label.ou' => ActiveDirectorySyncSourceType::OU
                ]
            ])
            ->add('source', TextType::class, [
                'label' => 'label.value'
            ])
            ->add('userType', EntityType::class, [
                'class' => UserTypeEntity::class,
                'label' => 'label.user_type',
                'choice_label' => 'name'
            ]);
    }
}