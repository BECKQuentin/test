<?php

namespace App\Form\Abstract;

use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

abstract class AbstractSearchType extends AbstractType
{
    public function __construct(
        private UserRepository          $userRepository,
        private Security                $security,
    ){}

    public function addQuerySearchInput(FormBuilderInterface $builder): void
    {
        $builder->add('q', TextType::class, [
            'label' => false,
            'required' => false,
            'attr' => [
                'placeholder' => 'Rechercher',
                'autofocus' => true
            ]
        ]);
    }


    //Permet de choisir les maitres spirituels seulement si c'est l'admin qui fait appel a ce formulaire
//    public function addVerifiedBy(FormBuilderInterface $builder): void
//    {
//        $users = $this->userRepository->findAtLeastWithRole('ROLE_SPIRITUAL_MASTER');
//
//        // Create an array of choices from the users
//        $choices = [];
//        foreach ($users as $user) {
//            $choices[$user->getFullName()] = $user->getId();
//        }
//
//        $builder->add('verifiedBy', ChoiceType::class, [
//            'required'      => false,
//            'multiple'      => false,
//            'label'         => false,
//            'placeholder'   => 'app.user.account_verified_by',
//            'choices'       => $choices,
//            'disabled'      => !$this->security->isGranted('ROLE_ADMIN'), // Disable the field if user is not an admin
//        ]);
//    }
//
//    public function addLocale(FormBuilderInterface $builder): void
//    {
//        $builder->add('locale', ChoiceType::class, [
//            'placeholder' => 'app.language',
//            'choices'   => array_combine(
//                array_map(fn(AppLocale $appLocale) => $appLocale->getName(), $this->appLocaleRepository->findAll()),
//                array_map(fn(AppLocale $appLocale) => $appLocale->getSlug(), $this->appLocaleRepository->findAll())
//            ),
//            'required'  => false,
//            'label'     => false,
//        ]);
//    }
//
//    public function addVerifiedBefore(FormBuilderInterface $builder): void
//    {
//        $builder->add('verifiedBefore', DateType::class, [
//            'label'     => 'Verified before',
//            'widget'    => 'single_text',
//            'required'  => false,
//        ]);
//    }
//
//    public function addVerifiedAfter(FormBuilderInterface $builder): void
//    {
//        $builder->add('verifiedAfter', DateType::class, [
//            'label'     => 'Verified after',
//            'widget'    => 'single_text',
//            'required'  => false,
//        ]);
//    }

    public function addSearchSubmit(FormBuilderInterface $builder): void
    {
        $builder->add('submit', SubmitType::class, [
            'label' => 'Rechercher',
            'attr' => [
                'class' => 'btn btn-primary'
            ]
        ]);
    }

//    public function configureOptions(OptionsResolver $resolver)
//    {
//        $resolver->setDefaults([
//            'data_class' => User::class,
//        ]);
//    }
}