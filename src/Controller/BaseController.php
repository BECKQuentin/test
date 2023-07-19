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

    #[Route('/', name: 'home')]
    public function home(): Response
    {
        return $this->render('home/home.html.twig', [

        ]);
    }

    #[Route('/redirect-user', name: 'redirect_user')]
    public function redirectUser(): RedirectResponse
    {

        if ($this->isGranted('ROLE_CONSULTANT')) {
            return $this->redirectToRoute('consultant_dashboard');
        }
        elseif ($this->isGranted('ROLE_INSTALLER')) {
            return $this->redirectToRoute('user_dashboard');
        }
        else {
            return $this->redirectToRoute('app_login');
        }
    }


    #[Route('/mentions-legales', name: 'legal_notice')]
    public function legalNotice()
    {
        return $this->render('base/legal_notice.html.twig', [

        ]);
    }

}