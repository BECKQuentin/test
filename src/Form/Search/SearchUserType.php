<?php

namespace App\Form\Search;

use App\Data\SearchUserData;
use App\Form\Abstract\AbstractSearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchUserType extends AbstractSearchType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->addQuerySearchInput($builder);
        $this->addSearchSubmit($builder);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchUserData::class,
            'method'    => 'GET',
        ]);
    }
}