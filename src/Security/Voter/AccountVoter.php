<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Repository\UserMatchRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AccountVoter extends Voter
{
    public const READ       = 'CAN_READ';

    public function __construct(
        private Security $security,
    ){}

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::READ])
            && ($subject instanceof User);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // ... (check conditions and return true to grant permission) ...
        return match ($attribute) {
            self::READ          => $this->canRead($user, $subject),
            default => false,
        };

    }

    //Peux lire si maps avec utilisateur en question ou si ADMIN
    private function canRead($user, $subject): bool
    {
        if ($subject === $user) { //redirige vers le dashboard de l'utilisateur si consulte sa propre fiche
            return false;
        }
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }
        //TODO: Regarder si lien entre les deu x ussers
//        if ($this->userMatchRepository->findMatchBetweenUsers($user, $subject)) {
//            return true;
//        }
        return false;
    }

}