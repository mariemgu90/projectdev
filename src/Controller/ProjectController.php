<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/project')]
class ProjectController extends AbstractController
{
    // Route pour la création d'un nouveau projet
    #[Route('/new', name: 'project_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($project);
            $entityManager->flush();

            $this->addFlash('success', 'Le projet a été publié avec succès !');
            return $this->redirectToRoute('project_list');
        }

        return $this->render('project/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Route pour la liste des projets
    #[Route('/projects', name: 'project_list', methods: ['GET'])]
    public function list(EntityManagerInterface $entityManager): Response
    {
        $projects = $entityManager->getRepository(Project::class)->findAll();

        return $this->render('project/list.html.twig', [
            'projects' => $projects,
        ]);
    }

    // Route pour afficher un projet spécifique
    #[Route('/{id}', name: 'project_show', methods: ['GET', 'POST'])]
    public function show(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $project = $entityManager->getRepository(Project::class)->find($id);

        if (!$project) {
            throw $this->createNotFoundException('Le projet demandé n\'existe pas.');
        }

        if ($request->isMethod('POST')) {
            $offer = $request->request->get('offer');
            // Traitement de l'offre ici

            $this->addFlash('success', 'Votre offre a été envoyée avec succès !');
            return $this->redirectToRoute('project_show', ['id' => $project->getId()]);
        }

        return $this->render('project/show.html.twig', [
            'project' => $project,
        ]);
    }

    // Route pour modifier un projet
    #[Route('/{id}/edit', name: 'project_edit', methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupérer le projet à partir de l'ID
        $project = $entityManager->getRepository(Project::class)->find($id);

        if (!$project) {
            throw $this->createNotFoundException('Le projet demandé n\'existe pas.');
        }

        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Le projet a été mis à jour avec succès !');

            return $this->redirectToRoute('project_list');
        }

        return $this->render('project/edit.html.twig', [
            'form' => $form->createView(),
            'project' => $project, // Assurez-vous de passer l'objet project à la vue
        ]);
    }

    // Route pour supprimer un projet
    #[Route('/{id}/delete', name: 'project_delete', methods: ['POST'])]
    public function delete(int $id, EntityManagerInterface $entityManager): Response
    {
        // Récupérer le projet à partir de l'ID
        $project = $entityManager->getRepository(Project::class)->find($id);

        if (!$project) {
            throw $this->createNotFoundException('Le projet demandé n\'existe pas.');
        }

        // Supprimer le projet
        $entityManager->remove($project);
        $entityManager->flush();

        $this->addFlash('success', 'Le projet a été supprimé avec succès !');
        return $this->redirectToRoute('project_list');
    }
}
