<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Form\ChangeEmailFormType;
use App\Form\ChangePasswordFormType;
use App\Form\UserEditType;
use App\Helper\AppHelper;
use App\Repository\UserRepository;
use App\Security\Voter\AccountVoter;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserController extends AbstractController
{
    public function __construct(

    ) {}


    #[Route('/show/{id}', name: 'user_show', options: ['expose' => true])]
    #[IsGranted(AccountVoter::READ, subject: 'user')]
    public function showUser(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user'              => $user,
        ]);

    }
}