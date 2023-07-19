<?php

namespace App\Form\Abstract;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

abstract class AbstractUserType extends AbstractType
{

    public function __construct(
    ){}


    public function addEmail(FormBuilderInterface $builder): void
    {
        $builder->add('email', EmailType::class, [
            'required'  => true,
            'label'     => 'Email',
        ]);
    }

    public function addFirstname(FormBuilderInterface $builder): void
    {
        $builder->add('firstname', TextType::class, [
            'required'  => true,
            'label'     => 'Prénom',
        ]);
    }

    public function addLastname(FormBuilderInterface $builder): void
    {
        $builder->add('lastname', TextType::class, [
            'required'  => true,
            'label'     => 'Nom',
        ]);
    }

    public function addPhone(FormBuilderInterface $builder): void
    {
        $builder->add('phone', TextType::class, [
            'label'         => 'Telephone fixe',
            'required'      => false,
        ]);
    }

    public function addMobile(FormBuilderInterface $builder): void
    {
        $builder->add('mobile', TextType::class, [
            'label'         => 'Telephone portable',
            'required'      => false,
        ]);
    }

    public function addAgreeTerms(FormBuilderInterface $builder): void
    {
        $builder->add('agreeTerms', CheckboxType::class, [
            'mapped'    => false,
            'constraints' => [
                new IsTrue([
                    'message' => 'page.auth.register.form.agree_terms.text',
                ]),
            ],
            'required' => true,
        ]);
    }

    public function addPlainPassword(FormBuilderInterface $builder):void
    {
        $builder->add('plainPassword', PasswordType::class, [
            // instead of being set onto the object directly,
            // this is read and encoded in the controller
            'label'     => 'mot de passe',
            'mapped'    => false,
            'attr'      => ['autocomplete' => 'new-password'],
            'constraints' => [
                new NotBlank([
                    'message' => 'page.auth.register.form.error.enter_password',
                ]),
                new Length([
                    'min' => 6,
                    // max length allowed by Symfony for security reasons
                    'max' => 4096,
                ]),
            ],
        ]);
    }

    public function addSubmit(FormBuilderInterface $builder): void
    {
        $builder->add('submit', SubmitType::class, [
            'label' => 'Editer',
            'attr' => [
                'class' => 'btn btn-primary'
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}