<?php
namespace App\Controller;

use App\Entity\User;
use App\Entity\Project;
use App\Entity\Freelancer;
use App\Entity\Payment;
use App\Form\ProjectType;
use App\Form\UserType;
use App\Service\FreelancerEvaluator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function dashboard(EntityManagerInterface $entityManager): Response
    {
        $totalUsers = $entityManager->getRepository(User::class)->count([]);
        $totalProjects = $entityManager->getRepository(Project::class)->count([]);
        $totalFreelancers = $entityManager->getRepository(Freelancer::class)->count([]);
        $pendingPayments = $entityManager->getRepository(Payment::class)->count(['status' => 'pending']);

        return $this->render('admin/dashboard.html.twig', [
            'totalUsers' => $totalUsers,
            'totalProjects' => $totalProjects,
            'totalFreelancers' => $totalFreelancers,
            'pendingPayments' => $pendingPayments,
        ]);
    }

    /** GESTION DES PROJETS */
    #[Route('/admin/projects', name: 'admin_manage_projects')]
    public function manageProjects(EntityManagerInterface $entityManager): Response
    {
        $projects = $entityManager->getRepository(Project::class)->findAll();

        // Assurer l'initialisation de `status` si nécessaire
        foreach ($projects as $project) {
            if ($project->getStatus() === null) {
                $project->setStatus('pending');
            }
        }

        $entityManager->flush();

        return $this->render('admin/manage_projects.html.twig', [
            'projects' => $projects,
        ]);
    }

    #[Route('/admin/projects/create', name: 'admin_create_project')]
    public function createProject(Request $request, EntityManagerInterface $entityManager): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            if (!$user) {
                $this->addFlash('error', 'Vous devez être connecté pour créer un projet.');
                return $this->redirectToRoute('admin_dashboard');
            }

            $project->setUser($user);
            $project->setStatus('open');
            $entityManager->persist($project);
            $entityManager->flush();

            $this->addFlash('success', 'Projet créé avec succès.');
            return $this->redirectToRoute('admin_manage_projects');
        }

        return $this->render('admin/edit_project.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/projects/edit/{id}', name: 'admin_edit_project')]
    public function editProject(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $project = $entityManager->getRepository(Project::class)->find($id);

        if (!$project) {
            throw $this->createNotFoundException('Projet non trouvé.');
        }

        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Projet mis à jour avec succès.');
            return $this->redirectToRoute('admin_manage_projects');
        }

        return $this->render('admin/edit_project.html.twig', [
            'form' => $form->createView(),
            'project' => $project,
        ]);
    }

    #[Route('/admin/projects/delete/{id}', name: 'admin_delete_project')]
    public function deleteProject(int $id, EntityManagerInterface $entityManager): Response
    {
        $project = $entityManager->getRepository(Project::class)->find($id);

        if (!$project) {
            throw $this->createNotFoundException('Projet non trouvé.');
        }

        $entityManager->remove($project);
        $entityManager->flush();

        $this->addFlash('success', 'Projet supprimé avec succès.');
        return $this->redirectToRoute('admin_manage_projects');
    }

    /** CHALLENGE POUR FREELANCERS */
    #[Route('/admin/projects/{id}/challenge', name: 'admin_project_challenge')]
    public function createChallenge(int $id, EntityManagerInterface $entityManager): Response
    {
        $project = $entityManager->getRepository(Project::class)->find($id);

        if (!$project) {
            throw $this->createNotFoundException('Projet non trouvé.');
        }

        $freelancers = $entityManager->getRepository(Freelancer::class)->findBy(['level' => 'beginner']);
        if (count($freelancers) < 3) {
            $this->addFlash('error', 'Pas assez de freelances débutants pour ce projet.');
            return $this->redirectToRoute('admin_manage_projects');
        }

        $selectedFreelancers = array_slice($freelancers, 0, 3);

        foreach ($selectedFreelancers as $index => $freelancer) {
            $amount = [1000, 500, 250][$index];

            $payment = new Payment();
            $payment->setFreelancer($freelancer)
                ->setProject($project)
                ->setAmount($amount)
                ->setStatus('pending');
            $entityManager->persist($payment);
        }

        $entityManager->flush();

        $this->addFlash('success', 'Challenge créé avec succès.');
        return $this->redirectToRoute('admin_manage_projects');
    }

    /** GESTION DES FREELANCERS */
    #[Route('/admin/freelancers', name: 'admin_manage_freelancers')]
    public function manageFreelancers(EntityManagerInterface $entityManager): Response
    {
        $freelancers = $entityManager->getRepository(Freelancer::class)->findAll();

        return $this->render('admin/manage_freelancers.html.twig', [
            'freelancers' => $freelancers,
        ]);
    }

    /** GESTION DES PAIEMENTS */
    #[Route('/admin/payments', name: 'admin_manage_payments')]
    public function managePayments(EntityManagerInterface $entityManager): Response
    {
        $payments = $entityManager->getRepository(Payment::class)->findAll();

        return $this->render('admin/manage_payments.html.twig', [
            'payments' => $payments,
        ]);
    }

    #[Route('/admin/payments/approve/{id}', name: 'admin_approve_payment')]
    public function approvePayment(int $id, EntityManagerInterface $entityManager): Response
    {
        $payment = $entityManager->getRepository(Payment::class)->find($id);

        if (!$payment) {
            throw $this->createNotFoundException('Paiement non trouvé.');
        }

        $payment->setStatus('approved');
        $entityManager->flush();

        $this->addFlash('success', 'Paiement approuvé avec succès.');
        return $this->redirectToRoute('admin_manage_payments');
    }

    /** GESTION DES UTILISATEURS */
    #[Route('/admin/users', name: 'admin_manage_users')]
    public function manageUsers(EntityManagerInterface $entityManager): Response
    {
        return $this->render('admin/manage_users.html.twig', [
            'users' => $entityManager->getRepository(User::class)->findAll(),
        ]);
    }

    #[Route('/admin/users/create', name: 'admin_create_user')]
    public function createUser(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Hashing du mot de passe
            $plainPassword = $form->get('password')->getData();
            $user->setPassword(password_hash($plainPassword, PASSWORD_BCRYPT));

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur créé avec succès.');
            return $this->redirectToRoute('admin_manage_users');
        }

        return $this->render('admin/create_user.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/users/edit/{id}', name: 'admin_edit_user')]
    public function editUser(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Utilisateur mis à jour avec succès.');
            return $this->redirectToRoute('admin_manage_users');
        }

        return $this->render('admin/edit_user.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
    #[Route('/admin/users/{id}', name: 'admin_view_user')]
    public function viewUser(int $id, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        return $this->render('admin/view_user.html.twig', [
            'user' => $user,
        ]);
    }


    #[Route('/admin/users/delete/{id}', name: 'admin_delete_user')]
    public function deleteUser(int $id, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('success', 'Utilisateur supprimé avec succès.');
        return $this->redirectToRoute('admin_manage_users');
    }
}
