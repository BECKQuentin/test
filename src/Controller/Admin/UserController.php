<?php

namespace App\Controller\Admin;

use App\Data\SearchUserData;
use App\Entity\User;
use App\Form\Search\SearchUserType;
use App\Form\UserEditType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/u')]
class UserController extends AbstractController
{
    public function __construct(
        private UserRepository          $userRepository,
        private EntityManagerInterface  $entityManager,
        private PaginatorInterface      $paginator,
    ){}

    #[Route('/', name: 'admin_users_index')]
    public function index(Request $request)
    {
        $data = new SearchUserData();
        $data->page = $request->get('page', 1);

        $searchForm = $this->createForm(SearchUserType::class, $data);
        $searchUsers = $this->userRepository->findNoDeletedOrderByCreatedAt('desc');

        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $searchUsers = $this->userRepository->searchUser($data);
        } else {
            $searchUsers = $this->userRepository->findNoDeletedOrderByCreatedAt('desc');
        }

        //Pagination
        $usersPaginate = $this->paginator->paginate(
            $searchUsers,
            $request->get('page', 1),
            15
        );

        return $this->render('admin/user/index.html.twig', [
            'searchForm' => $searchForm->createView(),
            'users' => $usersPaginate,
        ]);
    }

    #[Route('/add', name: 'admin_add_user')]
    public function addUser(Request $request): Response
    {

        $user = new User();
        $userForm = $this->createForm(UserEditType::class, $user);


        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {

            $this->entityManager->persist($user);
            $this->entityManager->flush();


            $this->addFlash('success', 'Sauvegardé');
            return $this->redirectToRoute('admin_edit_user', ['id' => $user->getId()]);
        }

        return $this->render('admin/user/add.html.twig', [
            'userForm' => $userForm->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'admin_edit_user')]
    public function editUser(User $user, Request $request): Response
    {
        $userForm = $this->createForm(UserEditType::class, $user);

        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {

            $this->entityManager->flush();

            $this->addFlash('success', 'Sauvegardé');
            return $this->redirectToRoute('admin_edit_user');
        }

        return $this->render('admin/user/edit.html.twig', [
            'userForm' => $userForm->createView(),
        ]);
    }

}