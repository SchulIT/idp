<?php

namespace App\Form;

use App\Entity\ActiveDirectorySyncSourceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ActiveDirectoryGradeSyncOptionType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('grade', TextType::class, [
                'label' => 'label.grade'
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
            ]);
    }
}