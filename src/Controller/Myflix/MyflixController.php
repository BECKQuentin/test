<?php

namespace App\Controller\Myflix;


use App\Repository\Myflix\VideoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class MyflixController extends AbstractController
{

    #[Route('/', name: 'myflix_index', methods: ['GET'])]
    public function index(VideoRepository $videoRepository): Response
    {
        return $this->render('project/project/index.html.twig', [
            'projects' => $videoRepository->findAll(),
        ]);
    }



}
