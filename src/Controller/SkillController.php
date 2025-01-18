<?php

namespace App\Controller;

use App\Entity\Skill;
use App\Form\SkillFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SkillController extends AbstractController
{
    #[Route('/skill', name: 'app_skill_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {   $user = $this->getUser();
        $skill = new Skill();

        // Create the form
        $form = $this->createForm(SkillFormType::class, $skill);

        // Handle the request
        $form->handleRequest($request);

        // Check if the form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            $skill->setUser($user);
            $entityManager->persist($skill);
            $entityManager->flush();

            // Redirect to a confirmation page or list of skills
            return $this->redirectToRoute('app_skill_new');
        }

        // Render the form
        return $this->render('skill.html.twig', [
            'skillForm' => $form->createView(),
        ]);
    }

    #[Route('/skills', name: 'app_skill_list')]
    public function list(EntityManagerInterface $entityManager): Response
    {
        // Retrieve the currently authenticated user
        $user = $this->getUser();

        // Retrieve only the skills that belong to the logged-in user
        $skills = $entityManager->getRepository(Skill::class)->findBy(['user' => $user]);

        return $this->render('skills.html.twig', [
            'skills' => $skills,
        ]);
    }
    #[Route('/skill/edit/{id}', name: 'app_skill_edit')]
    public function edit(Request $request, Skill $skill, EntityManagerInterface $entityManager): Response
    {
        // Ensure the logged-in user owns the skill
        if ($skill->getUser() !== $this->getUser()) {
            return $this->redirectToRoute('app_skill_list'); // Redirect if unauthorized
        }

        // Create the form for editing the skill
        $form = $this->createForm(SkillFormType::class, $skill);

        // Handle the form submission
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persist the updated skill
            $entityManager->flush();

            // Redirect to the list of skills after successful update
            $this->addFlash('success', 'Skill updated successfully!');
            return $this->redirectToRoute('app_skill_list');
        }

        // Render the form for editing
        return $this->render('skill/edit.html.twig', [
            'skillForm' => $form->createView(),
        ]);
    }

    #[Route('/skill/delete/{id}', name: 'app_skill_delete', methods: ['POST'])]
    public function delete(Request $request, Skill $skill, EntityManagerInterface $entityManager): Response
    {
        // Ensure the logged-in user owns the skill
        if ($skill->getUser() !== $this->getUser()) {
            return $this->redirectToRoute('app_skill_list'); // Redirect if unauthorized
        }

        // Check for CSRF protection
        if ($this->isCsrfTokenValid('delete' . $skill->getId(), $request->request->get('_token'))) {
            $entityManager->remove($skill); // Remove the skill
            $entityManager->flush(); // Commit the changes

            // Add a flash message to inform the user of the successful deletion
            $this->addFlash('success', 'Skill deleted successfully!');
        }

        // Redirect to the skills list page after deletion
        return $this->redirectToRoute('app_skill_list');
    }


}
