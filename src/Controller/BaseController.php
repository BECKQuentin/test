<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BaseController extends AbstractController
{

    public function __construct(
    ){}

    #[Route('/', name: 'base')]
    public function home(): Response
    {
        return $this->render('base.html.twig', [

        ]);
    }

    #[Route('/redirect-user', name: 'redirect_user')]
    public function redirectUser(): RedirectResponse
    {

        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('home');
        }
        elseif ($this->isGranted('ROLE_MEMBER')) {
            return $this->redirectToRoute('home');
        }
        elseif ($this->isGranted('ROLE_GUEST')) {
            return $this->redirectToRoute('home');
        }
        else {
            return $this->redirectToRoute('app_login');
        }
    }


    #[Route('/mentions-legales', name: 'legal_notice')]
    public function legalNotice()
    {
        return $this->render('security/legal_notice.html.twig', [

        ]);
    }

}