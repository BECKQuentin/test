<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AccountVoter extends Voter
{
    public const READ = 'CAN_READ';
    public const EDIT = 'CAN_EDIT';
    public const EDIT_CONTRACTOR_INSTALLERS = 'CAN_EDIT_CONTRACTOR_INSTALLERS';

    public function __construct(
        private Security $security,
    ){}

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::READ, self::EDIT, self::EDIT_CONTRACTOR_INSTALLERS])
            && ($subject instanceof User);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        return match ($attribute) {
            self::READ                      => $this->canRead($user, $subject),
            self::EDIT                      => $this->canEdit($user, $subject),
            self::EDIT_CONTRACTOR_INSTALLERS => $this->canEditContractorInstallers($user, $subject),
            default => false,
        };

    }

    //Peux lire si ROLE_CONSULTANT ou si ROLE_CONTRACTOR et que fait partie de ses installateurs
    private function canRead($user, $subject): bool
    {
        if ($subject === $user) {
            return false;
        }
        if ($this->security->isGranted('ROLE_CONSULTANT')) {
            return true;
        }
        return false;
    }

    //Peux editer si ROLE_CONSULTANT et pas son propre compte
    private function canEdit($user, $subject): bool
    {
        if ($subject === $user) {
            return false;
        }
        if (in_array(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'], $subject->getRoles()) && !$this->security->isGranted('ROLE_SUPER_ADMIN')) {
            return false;
        }
        if ($this->security->isGranted('ROLE_CONSULTANT')) {
            return true;
        }
        return false;
    }

    private function canEditContractorInstallers($user, $subject): bool
    {
        if ($subject === $user) {
            return false;
        }
        if (in_array(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'], $subject->getRoles()) && !$this->security->isGranted('ROLE_SUPER_ADMIN')) {
            return false;
        }
        if (!in_array('ROLE_CONTRACTOR', $subject->getRoles())) {
            return false;
        }
        if ($this->security->isGranted('ROLE_CONSULTANT')) {
            return true;
        }
        return false;
    }

}