<?php

namespace App\Controller\Project;

use App\Entity\Project\Images;
use App\Entity\Project\Project;
use App\Form\Project\ProjectType;
use App\Repository\Project\ProjectRepository;
use App\Service\UploadService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/project/project')]
class ProjectController extends AbstractController
{

    public function __construct(
        private UploadService $uploadService,
    ){}

    #[Route('/', name: 'app_project_project_index', methods: ['GET'])]
    public function index(ProjectRepository $projectRepository): Response
    {
        return $this->render('project/project/index.html.twig', [
            'projects' => $projectRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_project_project_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProjectRepository $projectRepository): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $projectRepository->save($project, true);

            return $this->redirectToRoute('app_project_project_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('project/project/new.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_project_project_show', methods: ['GET'])]
    public function show(Project $project): Response
    {
        return $this->render('project/project/show.html.twig', [
            'project' => $project,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_project_project_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Project $project, ProjectRepository $projectRepository): Response
    {
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $images = $form->get('images')->getData();
            if ($images) {
                foreach ($images as $image) {

                    $fileNameCode = $this->uploadService->createFileName($image, $project);
                    $fileName = $this->uploadService->upload($image, $fileNameCode);

                    $img = new Images();
                    $img->setSrc($fileName);
                    $img->setProject($project);
                    $project->addImage($img);

                }
            }


            $projectRepository->save($project, true);

            return $this->redirectToRoute('app_project_project_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('project/project/edit.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_project_project_delete', methods: ['POST'])]
    public function delete(Request $request, Project $project, ProjectRepository $projectRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$project->getId(), $request->request->get('_token'))) {
            $projectRepository->remove($project, true);
        }

        return $this->redirectToRoute('app_project_project_index', [], Response::HTTP_SEE_OTHER);
    }
}
