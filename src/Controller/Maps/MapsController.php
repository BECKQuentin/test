<?php

namespace App\Controller\Maps;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;


#[Route('/maps')]
class MapsController extends AbstractController
{


    public function __construct(
        private readonly UserRepository         $userRepository,
        private readonly SerializerInterface    $serializer,
    ){}


    #[Route('/', name: 'maps_index')]
//    #[IsGranted(AccountVoter::READ, subject:'user')]
    public function index()
    {

        /** @var User $user */
        $user = $this->getUser();

        $nearbyUsers = $this->userRepository->findNearest(45, 45, $user);

        dd($nearbyUsers);

        return $this->render('maps/index.html.twig', [
            'jsonUser' => $this->serializer->serialize($user, 'json', ['groups' => 'basic']),
        ]);
    }


    #[Route('/search-closest/{longitude}/{latitude}', name: 'maps_search_closest', options: ['expose' => true])]
//    #[IsGranted(AccountVoter::READ, subject:'user')]
    public function searchClosest(float $longitude, float $latitude, Request $request)
    {

        /** @var User $currentUser */
        $user = $this->getUser();

        $nearbyUsers = $this->userRepository->findNearest($longitude, $latitude, $user);

        return $this->json($nearbyUsers, context: ['groups' => 'basic']);
    }
}