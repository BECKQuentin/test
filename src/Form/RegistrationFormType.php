<?php

namespace App\Form;

use App\Form\Abstract\AbstractUserType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationFormType extends AbstractUserType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->addEmail($builder);
        $this->addFirstname($builder);
        $this->addLastname($builder);
        $this->addPlainPassword($builder);
        $this->addAgreeTerms($builder);
    }
}
