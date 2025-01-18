<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]

    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        // Create a new User entity
        $user = new User();

        // Create the registration form
        $form = $this->createForm(RegistrationFormType::class, $user);

        // Handle the request
        $form->handleRequest($request);

        // Check if the form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            // Encode the password
            $password = $form->get('password')->getData();
            if ($password) {
                $hashedPassword = $passwordHasher->hashPassword($user, $password);
                $user->setPassword($hashedPassword);
            }

            // Save the user to the database
            $entityManager->persist($user);
            $entityManager->flush();
            $roles = $user->getRoles();

            if (in_array('ROLE_CLIENT', $roles)) {
                return $this->redirectToRoute('app_login');
            } elseif (in_array('ROLE_FREELANCER', $roles)) {
                return $this->redirectToRoute('app_login'); }
        }

        // Render the registration form
        return $this->render('/Registration.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
