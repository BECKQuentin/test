<?php

namespace App\Form;

use App\Entity\Contractor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContractorUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', UserEditType::class)

            ->add('num_contrat_cadre')
            ->add('social_reason')
            ->add('num_siret')
            ->add('address')
            ->add('postal_code')
            ->add('city')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contractor::class,
        ]);
    }
}