<?php

namespace App\Manager;

use App\Entity\Consultant;
use App\Entity\Contractor;
use App\Entity\Installer;
use App\Entity\User;
use App\Helper\AppHelper;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;

class UserManager
{
    public function __construct(
        private AppHelper                   $appHelper,
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface      $entityManager,
        private LoginLinkHandlerInterface   $loginLinkHandler,
        private EmailService                $emailService,
    ){}

    public function canUserHaveRole(User $user): bool
    {
        foreach (User::LIST_UNIQUE_ROLES as $role) {
            if (in_array($role, $user->getRoles())) {
                return false; // L'utilisateur a un des rôles de la liste
            }
        }
        return true; // L'utilisateur n'a aucun des rôles de la liste

    }

//    public function findHighestRole(User $user, $hierarchy)
//    {
//        $highestRole = User::ROLE_USER;
//        foreach ($hierarchy as $k => $role) {
//            if (in_array($k, $user->getRoles())) {
//                $highestRole =  constant('App\\Entity\\User\\User::' . $k);
//                break;
//            }
//        }
//        return $highestRole;
//    }

    public function sendLoginLink(User $user): void
    {
        //création de lien de connection
        $loginLinkDetails = $this->loginLinkHandler->createLoginLink($user);
        $this->emailService->send([
            'to' => $user->getEmail(),
            'subject' => '[Important] Bienvenue à vous sur VodouCollector',
            'template' => 'email/member/member_add.html.twig',
            'context' => [
                'user' => $user,
                'loginLink' => $loginLinkDetails
            ]
        ]);
    }

    public function setRandomPassword(User $user): void
    {
        // hash the password (based on the security.yaml config for the $user class)
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $this->appHelper->generateToken(16)
        );
        $user->setPassword($hashedPassword);
    }
}