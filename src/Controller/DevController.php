<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserVibratoryLevel;
use App\Helper\AppHelper;
use App\Manager\UserManager;
use App\Repository\AppLocaleRepository;
use App\Repository\CountryRepository;
use App\Service\Mailjet\Email;
use App\Service\Mailjet\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * This Controller is only available in DEV
 * Its purpose is to create helper urls
 */
#[Route('/_dev', name: 'dev_', condition: "request.server.get('APP_ENV') in ['dev','preprod']")]
class DevController extends AbstractController
{
//    public function __construct(
//        private AppLocaleRepository     $appLocaleRepository,
//        private EntityManagerInterface  $entityManager,
//        private CountryRepository       $countryRepository,
//        private UserManager             $userManager,
//    ){}
//
//
//    #[Route('', name: 'dev')]
//    public function dev(): Response
//    {
//        return new Response('ok');
//    }
//
//    #[Route('/calculate-life-path/{id}', name: 'calculate_life_path')]
//    public function calculateLifePath(User $user): Response
//    {
//        $this->userManager->setLifePath($user);
//        $this->entityManager->flush();
//        return new Response('LifePath is : ' . $user->getLifePath());
//    }
//
//    #[Route('/email-test', name: 'test_email')]
//    public function testEmail(EmailService $emailService): Response
//    {
//        $email = new Email();
//        $email->setTemplateId(4599670);
//        $email->setSendToAdmin(true);
//        $email->setContext(['expiresAtMessageKey' => 'test']);
//
//        $emailService->sendEmail($email);
//
//        return $this->redirectToRoute('home');
//    }
//
//
//    #[Route('/create-random-user/{number}')]
//    public function createRandomUser(int $number = 1, UserPasswordHasherInterface $userPasswordHasher): RedirectResponse
//    {
//        for ($i = 0; $i < $number; $i++) {
//            $faker      = Factory::create();
//            $firstname  = $faker->firstName();
//            $lastname   = $faker->lastName();
//            $email      = 'soulover.dev+'.trim(strtolower($firstname.'.'.$lastname)).'@gmail.com';
//            $locales = $this->appLocaleRepository->findAll();
//
//            $user = new User();
//            $user->setEmail($email)
//                ->setRoles($this->randomRoles())
//                ->setPassword($userPasswordHasher->hashPassword($user, $faker->password()))
//                ->setFirstname($firstname)
//                ->setLastname($lastname)
//                ->setBirthDate(new \DateTime('now'))
//                ->setGender(User::GENDERS[array_rand(User::GENDERS)])
//                ->setMarking(rand(0, 99) < 5 ? User::MARKING_BLOCKED : User::MARKINGS[array_rand(User::MARKINGS)])
//                ->setLocale($locales[array_rand($locales)]->getSlug())
//                ->setIsAccountVerified(rand(0, 99) < 80)
//                ->setVibratoryLevel(rand(0, 150))
//                ->setCountry($this->countryRepository->find(rand(1, 231)))
//                ->setSponsorshipToken($this->userManager->generateNewSponsorshipToken())
//                ->setLoveStatus(User::LOVE_STATUSES[array_rand(User::LOVE_STATUSES)])
//                ->setPoints(rand(0,150))
//                ->setAstrologicalSign(AppHelper::ASTROLOGICAL_SIGNS[array_rand(AppHelper::ASTROLOGICAL_SIGNS)])
//            ;
//            $this->entityManager->persist($user);
//        }
//        $this->entityManager->flush();
//        $this->addFlash('success', $number. ' user have been created !');
//        return $this->redirectToRoute('home');
//    }
//
//
//    private function randomRoles(): array
//    {
//        $roles = [];
//        $arrRoles = User::ROLES;
//        $rand = rand(0, count($arrRoles)-1);
//        $roles[] = $arrRoles[$rand];
//        // Ajout des roles SPIRIT ou TRANSLATOR avec faible proportionnalité
//        if ($arrRoles[$rand] === 'ROLE_SPIRITUAL_MASTER') rand(1, 4) > 3 ?? $roles[] = 'ROLE_TRANSLATOR';
//        elseif ($arrRoles[$rand] === 'ROLE_TRANSLATOR') rand(1, 4) > 3 ?? $roles[] = 'ROLE_SPIRITUAL_MASTER';
//        return $roles;
//    }


}