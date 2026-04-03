<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Template;
use App\Models\Offer;
use App\Models\Company;
use App\Models\Skill;
use App\Models\Wishlist;
use App\Middleware\AuthMiddleware;

class OfferController
{
    private Offer $offerModel;

    public function __construct()
    {
        $this->offerModel = new Offer();
    }

    /** GET /offres */
    public function index(): void
    {
        // 1. On attrape le numéro de page dans l'URL 
        $page = max(1, (int)($_GET['page'] ?? 1));
        
        // 2. On garde tes éventuels filtres de recherche
        $search = trim($_GET['search'] ?? '');
        
        $offerModel = new \App\Models\Offer();
        
        // 3. On utilise la méthode search() 
        $result = $offerModel->search($page, 12, $search);

        // 4. On envoie l'objet 'offers' complet à la vue 
        \App\Core\Template::render('offers/index.html.twig', [
            'offers'  => $result,
            'filters' => ['search' => $search]
        ]);
    }

    /** GET /offres/{id} */
    public function show(string $id): void
    {
        $offer = $this->offerModel->findFull((int)$id);
        if (!$offer) { $this->abort404(); return; }

        $inWishlist = false;
        $hasApplied = false;
        $myCompany = null;

        if (Auth::check()) {
            if (Auth::role() === 'etudiant') {
                $wl = new Wishlist();
                $inWishlist = $wl->has(Auth::id(), (int)$id);
                $appModel   = new \App\Models\Application();
                $hasApplied = $appModel->exists((int)$id, Auth::id());
            } elseif (Auth::role() === 'entreprise') {
                $myCompany = (new Company())->findByUser(Auth::id());
            }
        }

        Template::render('offers/show.html.twig', [
            'offer'      => $offer,
            'in_wishlist'=> $inWishlist,
            'has_applied'=> $hasApplied,
            'my_company' => $myCompany,
        ]);
    }

    /** GET /offres/creer */
    public function create(): void
    {
        AuthMiddleware::requireRole('admin', 'pilote', 'entreprise');
        $myCompany = (Auth::role() === 'entreprise') ? (new Company())->findByUser(Auth::id()) : null;
        
        Template::render('offers/form.html.twig', [
            'offer'      => null,
            'skills'     => (new Skill())->all(),
            'companies'  => (new Company())->all(),
            'my_company' => $myCompany,
            'today'      => date('Y-m-d'), 
        ]);
    }

    /** POST /offres/creer */
    public function store(): void
    {
        AuthMiddleware::requireRole('admin', 'pilote', 'entreprise');
        AuthMiddleware::verifyCsrf();

        $data = $this->extractFormData();
        
        if (Auth::role() === 'entreprise') {
            $company = (new Company())->findByUser(Auth::id());
            if ($company) { 
                $data['entreprise_id'] = (int)$company['id']; 
            }
        }

        if (empty($data['date_publication'])) {
            $data['date_publication'] = date('Y-m-d');
        }

        $errors = $this->validate($data);

        if ($errors) {
            $myCompany = (Auth::role() === 'entreprise') ? (new Company())->findByUser(Auth::id()) : null;
            Template::render('offers/form.html.twig', [
                'offer'      => null,
                'skills'     => (new Skill())->all(),
                'companies'  => (new Company())->all(),
                'my_company' => $myCompany,
                'errors'     => $errors,
                'old'        => $data,
                'today'      => date('Y-m-d'),
            ]);
            return;
        }

        $skillIds = array_map('intval', (array)($_POST['skills'] ?? []));
        $id = $this->offerModel->create($data, $skillIds);
        
        header("Location: /offres/{$id}");
        exit;
    }

    /** GET /offres/{id}/modifier */
    public function edit(string $id): void
    {
        AuthMiddleware::requireRole('admin', 'pilote', 'entreprise');
        $offer = $this->offerModel->findFull((int)$id);
        if (!$offer) { $this->abort404(); return; }

        $myCompany = (Auth::role() === 'entreprise') ? (new Company())->findByUser(Auth::id()) : null;
        
        $ownerId = $offer['entreprise_id'] ?? 0;
        if (Auth::role() === 'entreprise' && (!$myCompany || $ownerId != $myCompany['id'])) {
            die("Accès refusé.");
        }

        Template::render('offers/form.html.twig', [
            'offer'            => $offer,
            'skills'           => (new Skill())->all(),
            'companies'        => (new Company())->all(),
            'my_company'       => $myCompany,
            'selected_skills'  => array_column($offer['skills'] ?? [], 'id'),
            'today'            => date('Y-m-d'),
        ]);
    }

    /** POST /offres/{id}/modifier */
    public function update(string $id): void
    {
        AuthMiddleware::requireRole('admin', 'pilote', 'entreprise');
        AuthMiddleware::verifyCsrf();

        $offer = $this->offerModel->find((int)$id);
        if (!$offer) { $this->abort404(); return; }

        $data = $this->extractFormData();
        if (Auth::role() === 'entreprise') {
            $company = (new Company())->findByUser(Auth::id());
            $data['entreprise_id'] = (int)$company['id'];
        }

        $errors = $this->validate($data);

        if ($errors) {
            $myCompany = (Auth::role() === 'entreprise') ? (new Company())->findByUser(Auth::id()) : null;
            Template::render('offers/form.html.twig', [
                'offer'      => $offer,
                'skills'     => (new Skill())->all(),
                'companies'  => (new Company())->all(),
                'my_company' => $myCompany,
                'errors'     => $errors,
                'old'        => $data,
                'today'      => date('Y-m-d'),
            ]);
            return;
        }

        $skillIds = array_map('intval', (array)($_POST['skills'] ?? []));
        $this->offerModel->update((int)$id, $data, $skillIds);
        header("Location: /offres/{$id}");
        exit;
    }

    /** POST /offres/{id}/supprimer */
    public function destroy(string $id): void
    {
        AuthMiddleware::requireRole('admin', 'pilote', 'entreprise');

        $offer = $this->offerModel->find((int)$id);
        if (!$offer) { $this->abort404(); return; }

        // Sécurité supplémentaire pour les entreprises
        if (Auth::role() === 'entreprise') {
            $myCompany = (new Company())->findByUser(Auth::id());
            if (!$myCompany || $offer['entreprise_id'] != $myCompany['id']) {
                die("Accès refusé : Cette offre ne vous appartient pas.");
            }
        }

        $this->offerModel->delete((int)$id);
        header('Location: /offres?success=deleted');
        exit;
    }

    private function extractFormData(): array
    {
        return [
            'titre'            => trim($_POST['titre'] ?? ''),
            'description'      => trim($_POST['description'] ?? ''),
            'remuneration'     => (float)($_POST['remuneration'] ?? 0),
            'date_publication' => trim($_POST['date_publication'] ?? ''),
            'entreprise_id'    => (int)($_POST['entreprise_id'] ?? 0),
            'duree_mois'       => (int)($_POST['duree_mois'] ?? 0), 
        ];
    }

    private function validate(array $data): array
    {
        $errors = [];
        if (empty($data['titre']))            $errors[] = 'Le titre est obligatoire.';
        if (empty($data['description']))      $errors[] = 'La description est obligatoire.';
        if ($data['entreprise_id'] <= 0)      $errors[] = 'Erreur entreprise.';
        
        if ($data['remuneration'] < 0) {
            $errors[] = 'La rémunération doit être positive.';
        } elseif ($data['remuneration'] >= 10000) {
            $errors[] = 'La rémunération ne peut pas dépasser 9 999 €.';
        }

        if ($data['duree_mois'] <= 0)         $errors[] = 'La durée doit être supérieure à 0.';
        return $errors;
    }

    private function abort404(): void
    {
        http_response_code(404);
        Template::render('errors/404.html.twig');
    }
}
