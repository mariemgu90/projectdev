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
        // Retrieve all skills from the database
        $skills = $entityManager->getRepository(Skill::class)->findAll();

        return $this->render('skills.html.twig', [
            'skills' => $skills,
        ]);
    }


}
