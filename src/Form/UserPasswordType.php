<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserPasswordType extends AbstractType
{
 public function buildForm(FormBuilderInterface $builder, array $options)
 {
    $builder
        ->add('newPlainPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'first_options' => [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Nouveau mot de passe',
                'label_attr' => [
                    'class' => 'form-label'
                ]
            ],
            'second_options' => [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Confirmation du Nouveau mot de passe',
                'label_attr' => [
                    'class' => 'form-label'
                ]
            ],
            'invalid_message' => 'Les mots de passe ne sont pas identiques'
        ])

        ->add('plainPassword', PasswordType::class, [
            'attr' => [
                'class' => 'form-control'
            ],
            'label' => 'Mot de passe',
            'label_attr' => [
                'class' => 'form-label'
            ],
            'constraints' => [
                new NotBlank()
            ]
        ])

        ->add('submit', SubmitType::class, [
            'attr' => [
                'class' => 'btn btn-dark mt-2'
            ],
            'label' => 'Modifier'

        ]);
 }
}