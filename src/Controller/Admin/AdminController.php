<?php

namespace App\Controller\Admin;

use App\Entity\Consultant;
use App\Entity\Contractor;
use App\Entity\Installer;
use App\Entity\User;
use App\Form\ConsultantUserType;
use App\Form\ContractorUserType;
use App\Form\InstallerUserType;
use App\Form\UserEditType;
use App\Repository\UserRepository;
use App\Security\Voter\AccountVoter;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/u')]
class AdminController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface  $entityManager
    ){}

    #[Route('/add/consultant', name: 'admin_add_consultant')]
    public function addUser(Request $request): Response
    {

        $consultant = new Consultant();
        $userForm = $this->createForm(ConsultantUserType::class, $consultant);


        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {

            $consultant->getUser()->addRole('ROLE_CONSULTANT');

            $this->entityManager->persist($consultant);
            $this->entityManager->flush();


            $this->addFlash('success', 'Sauvegardé');
            return $this->redirectToRoute('consultant_add_contractor');
        }

        return $this->render('consultant/user/add.html.twig', [
            'userForm' => $userForm->createView(),
        ]);
    }

}