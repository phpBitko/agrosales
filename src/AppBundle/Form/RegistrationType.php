<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\{
    EmailType, TextareaType, TextType
};
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Електронна адреса',
                'constraints' => new Email(),
                'attr' => [
                    'class' => 'form-control',
                    'title' => 'email'
                ]
            ])
            ->add('username', TextType::class, [
                'label' => 'Логін',
                'constraints' => new NotBlank(),
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('plainPassword', RepeatedType::class, [
                'first_options'  => array('label' => 'Пароль', 'constraints' => new NotBlank(),
                    'attr' => [
                        'class' => 'form-control'
                    ]),
                'second_options' => array('label' => 'Повторити пароль', 'constraints' => new NotBlank(),
                    'attr' => [
                        'class' => 'form-control'
                    ]),
                'type' => PasswordType::class
            ]);
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';
    }

    public function getBlockPrefix()
    {
        return 'app_user_registration';
    }
}