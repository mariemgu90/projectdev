<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
       EntityManagerInterface $entityManager
    ): Response {
        // Create a new User entity
        $user = new User();

        // Create the registration form
        $form = $this->createForm(RegistrationFormType::class, $user);

        // Handle the request
        $form->handleRequest($request);

        // Check if the form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            // Hash the password
            $password = $form->get('password')->getData();
            if ($password) {
                $hashedPassword = $passwordHasher->hashPassword($user, $password);
                $user->setPassword($hashedPassword);
            }

            try {
                // Persist the user to the database
                $entityManager->persist($user);
                $entityManager->flush();

                // Redirect based on the user's role
                $roles = $user->getRoles();
                if (in_array('ROLE_CLIENT', $roles)) {
                    return $this->redirectToRoute('app_login'); // Or redirect to the 'client' dashboard if needed
                } elseif (in_array('ROLE_FREELANCER', $roles)) {
                    return $this->redirectToRoute('app_login'); // Or redirect to the 'freelancer' dashboard if needed
                }

            } catch (UniqueConstraintViolationException $e) {
                // Handle the case when the email is already in the database
                $this->addFlash('error', 'The email address is already in use. Please choose a different one.');
            }
        }

        // Render the registration form
        return $this->render('registration.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
