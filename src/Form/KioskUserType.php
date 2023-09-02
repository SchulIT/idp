<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class KioskUserType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('user', EntityType::class, [
                'label' => 'label.user',
                'class' => User::class,
                'choice_label' => fn(User $user) => $user->getUsername(),
                'placeholder' => 'label.select',
                'attr' => [
                    'data-choice' => 'true'
                ]
            ])
            ->add('ipAddresses', TextType::class, [
                'label' => 'label.ip_addresses.label',
                'help' => 'label.ip_addresses.help',
            ]);
    }
}