<?php

namespace App\Controllers;

use App\Core\Template;
use App\Models\Offer;
use App\Middleware\AuthMiddleware;

class StatsController
{
    public function index(): void
    {
        // On autorise maintenant les étudiants, les pilotes et les admins
        AuthMiddleware::requireRole('admin', 'pilote', 'etudiant');

        $offerModel = new Offer();
        $stats = $offerModel->stats();

        Template::render('stats/index.html.twig', [
            'stats' => $stats
        ]);
    }
}
