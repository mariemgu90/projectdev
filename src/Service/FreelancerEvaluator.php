<?php

namespace App\Service;

use App\Entity\Freelancer;

class FreelancerEvaluator
{
    /**
     * Évalue un freelance et met à jour son niveau.
     *
     * @param Freelancer $freelancer
     */
    public function evaluateFreelancer(Freelancer $freelancer): void
    {
        // Exemples de critères d'évaluation (ces critères peuvent être modifiés selon votre logique)
        $performanceScore = $this->calculatePerformanceScore($freelancer);

        // Logique pour mettre à jour le niveau du freelance
        if ($performanceScore >= 80) {
            $freelancer->setLevel('advanced');
        } elseif ($performanceScore >= 50) {
            $freelancer->setLevel('intermediate');
        } else {
            $freelancer->setLevel('beginner');
        }

        // Vous pouvez ajouter d'autres règles selon la performance ou l'expérience.
    }

    /**
     * Calcule un score de performance basé sur les projets terminés, les avis, etc.
     *
     * @param Freelancer $freelancer
     * @return int
     */
    private function calculatePerformanceScore(Freelancer $freelancer): int
    {
        // Exemple : calcul du score en fonction du nombre de projets terminés
        $completedProjects = count($freelancer->getProjects()); // Vous pouvez ajuster ce calcul
        $rating = $freelancer->getRating(); // Supposez que vous ayez un système de notation (1-5)

        // Exemple de calcul du score (juste un modèle de calcul)
        $score = $completedProjects * 10 + ($rating * 20);

        // Limiter le score maximum à 100
        return min(100, $score);
    }
}
