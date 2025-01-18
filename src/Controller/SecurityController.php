<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // If the user is already logged in, redirect to a specific route (e.g. homepage)
        if ($this->getUser()) {
            return $this->redirectToRoute('app_skill_new'); // Replace with your desired route
        }

        // Get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // Get the last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();


        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        // This method will be intercepted by Symfony's security system
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
