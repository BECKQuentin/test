<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
#[\Attribute] class PhoneNumber extends Constraint
{
    public string $message = 'Le numéro de téléphone "{{ value }}" n\'est pas valide.';
}
