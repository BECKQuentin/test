<?php

namespace App\Controller\User;


use App\Entity\User;
use App\Form\ChangePasswordFormType;
use App\Form\UserEditType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface  $entityManager,
    ){}

    #[Route('/edit', name: 'user_edit')]
    public function editUser(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $userForm = $this->createForm(UserEditType::class, $user);
        $userForm->remove('email');
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $user->setUpdatedAt(new \DateTime('NOW'));

            $this->entityManager->flush();

            $this->addFlash('success', 'Sauvegardé');
            return $this->redirectToRoute('user_edit');
        }

        return $this->render('user/edit.html.twig', [
            'user'              => $user,
            'userForm'          => $userForm->createView(),
        ]);

    }


    #[Route('/change-password', name: 'user_change_password')]
    public function changePassword(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();

            // Vérifie le mot de passe actuel
            if (!password_verify($form->get('old_password')->getData(), $user->getPassword())) {
                $this->addFlash('danger', 'Le mot de passe actuel est incorrect');
                return $this->redirectToRoute('user_change_password');
            }

            $encodedPassword = $passwordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            );

            // Enregistre les modifications
            $user->setPassword($encodedPassword);
            $this->entityManager->flush();

            // Redirige vers la page de confirmation
            $this->addFlash('success', 'Mot de passe modifié avec succés');
            return $this->redirectToRoute('user_dashboard');
        }

        return $this->render('user/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

//    #[Route('/change-email', name: 'user_change_email')]
//    public function changeEmail(Request $request): Response
//    {
//        $form = $this->createForm(ChangeEmailFormType::class);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            /** @var User $user */
//            $user = $this->getUser();
//
//            $newEmail = $form->get('email')->getData();
//            if ($this->userRepository->findOneBy(['email' => $newEmail])) {
//                $this->addFlash('danger', 'email déjà utilisé');
//                return $this->redirectToRoute('user_change_email');
//            }
//
//            $expiresAt = (new \DateTime('+2 hours'))->format('Y-m-d/H:i:s');
//            $token = AppHelper::crypt(json_encode([
//                'userId' => $user->getId(),
//                'newEmail' => $newEmail,
//                'expiresAt' => $expiresAt,
//            ]));
//            $context = [
//                'link' => $this->generateUrl('user_verify_changed_email', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL),
//                'expiresAt' => $expiresAt,
//            ];
//
//            $this->emailService->send([
//                'to'        => $user->getEmail(),
//                'subject'   => 'Changement d\'addresse email',
//                'template'  => 'email/user/change_email.html.twig',
//                'context'   => $context,
//            ]);
//
//
//            $this->addFlash('success', 'Merci de vérifier votre nouvel Email');
//            return $this->redirectToRoute('redirect_user');
//
//        }
//        return $this->render('user/change_email.html.twig', [
//            'form' => $form->createView(),
//        ]);
//    }
}