<?php

namespace App\Controller\Gemela;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/', name: 'gemela_index')]
    public function index(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('user/profile.html.twig', [
            'user'              => $user,
        ]);
    }
}