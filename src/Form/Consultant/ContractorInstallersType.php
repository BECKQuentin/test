<?php

namespace App\Form\Consultant;


use App\Entity\Contractor;
use App\Entity\Installer;
use App\Repository\InstallerRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContractorInstallersType extends AbstractType
{

    public function __construct(
        private InstallerRepository $installerRepository
    ){}
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('installer', EntityType::class, [
                'class'         => Installer::class,
                'label'         => 'Installateurs',
                'choice_label'  => 'socialReason',
                'mapped'        => true,
                'required'      => true,
                'multiple'      => true,
                'query_builder' => function(TypologyRepository $typologyRepository) {
                    return $typologyRepository->createQueryBuilder('t')
                        ->orderBy('t.name', 'ASC');
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contractor::class,
        ]);
    }
}