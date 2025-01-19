<?php
namespace App\Controller\Admin;

use App\Repository\UserRepository;
use App\Repository\ProjectRepository;
use App\Repository\FreelancerRepository;
use App\Repository\PaymentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
#[Route('/admin/dashboard', name: 'admin_dashboard')]
public function index(
UserRepository $userRepository,
ProjectRepository $projectRepository,
FreelancerRepository $freelancerRepository,
PaymentRepository $paymentRepository
): Response {
$this->denyAccessUnlessGranted('ROLE_ADMIN');

// Récupération des statistiques
$totalUsers = $userRepository->count([]);
$totalProjects = $projectRepository->count([]);
$totalFreelancers = $freelancerRepository->count([]);
$pendingPayments = $paymentRepository->count(['status' => 'pending']);

return $this->render('admin/dashboard.html.twig', [
'totalUsers' => $totalUsers,
'totalProjects' => $totalProjects,
'totalFreelancers' => $totalFreelancers,
'pendingPayments' => $pendingPayments,
]);
}
}
