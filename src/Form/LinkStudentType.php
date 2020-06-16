<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;

class LinkStudentType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('username', EmailType::class, [
                'label' => 'label.username'
            ])
            ->add('password', PasswordType::class, [
                'label' => 'label.password'
            ]);
    }
}