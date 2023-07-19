<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{

    public function __construct(
    ){}

    public function checkPreAuth(UserInterface $user): void
    {
        /** @var \App\Entity\User $user */
        if ($user->getMarking() === \App\Entity\User::MARKING_EMAIL_VERIFICATION) {
            throw new CustomUserMessageAuthenticationException("Vous devez d'abord vérifier votre email, consultez votre boîte mail.");
        }
        if ($user->getMarking() === \App\Entity\User::MARKING_BLOCKED) {
            throw new CustomUserMessageAuthenticationException("Désolé votre compte est bloqué.");
        }
        if ($user->getMarking() === \App\Entity\User::MARKING_DELETED) {
            throw new CustomUserMessageAuthenticationException("Désolé mais ce compte a été supprimé.");
        }
    }

    public function checkPostAuth(UserInterface $user)
    {

    }
}