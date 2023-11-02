<?php

namespace App\Security\Voter;

use App\Entity\Chat;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ChatMessageVoter extends Voter
{
    public const READ = 'chat_message-read';
    public const WRITE = 'chat_message-write';
    public const TALK = 'chat_message-talk';

    public function __construct(
        private Security $security,
    ){}

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::READ, self::WRITE, self::TALK])
            && ($subject instanceof Chat || $subject === null);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // ... (check conditions and return true to grant permission) ...
        return match ($attribute) {
            self::READ => $this->canRead($user, $subject),
            self::WRITE => $this->canWrite($user, $subject),
            default => false,
        };

    }

    //Peux lire si l'utilisateur fait partie du tchat en question ou si ADMIN
    private function canRead($user, $subject): bool
    {
        if ($subject === null) { //permet d'accéder a la route index
            return true;
        }
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }
        if ($user === $subject->getUser1() || $user === $subject->getUser2()) {
            return true;
        }
        return false;
    }

    //Peux écrire si l'utilisateur fait partie du tchat en question
    private function canWrite($user, $subject): bool
    {
        return $user === $subject->getUser1()
            || $user === $subject->getUser2();
    }
}