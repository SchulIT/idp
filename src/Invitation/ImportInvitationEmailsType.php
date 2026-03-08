<?php

namespace App\Invitation;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImportInvitationEmailsType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('csv', TextareaType::class, [
                'label' => 'codes.invitation.csv.label',
                'help' => 'codes.invitation.csv.help',
            ])
            ->add('delimiter', TextType::class, [
                'label' => 'codes.invitation.csv.delimiter.label',
                'help' => 'codes.invitation.csv.delimiter.help'
            ])
            ->add('emailHeader', TextType::class, [
                'label' => 'codes.invitation.csv.header.email.label',
                'help' => 'codes.invitation.csv.header.email.help'
            ])
            ->add('studentHeader', TextType::class, [
                'label' => 'codes.invitation.csv.header.student.label',
                'help' => 'codes.invitation.csv.header.student.help'
            ])
            ->add('createCodeIfNotExist', CheckboxType::class, [
                'label' => 'codes.invitation.csv.create_if_not_exist.label',
                'help' => 'codes.invitation.csv.create_if_not_exist.help',
                'required' => false
            ]);
    }
}