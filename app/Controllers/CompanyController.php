<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Template;
use App\Models\Company;
use App\Models\Offer;
use App\Models\Application;
use App\Models\Review;
use App\Models\User;
use App\Middleware\AuthMiddleware;

class CompanyController
{
    private Company $companyModel;

    public function __construct()
    {
        $this->companyModel = new Company();
    }

    public function create(): void
    {
        AuthMiddleware::requireRole('admin', 'pilote');
        Template::render('companies/form.html.twig');
    }

    public function store(): void
    {
        AuthMiddleware::requireRole('admin', 'pilote');
        AuthMiddleware::verifyCsrf();

        $companyData = [
            'nom'               => trim($_POST['nom'] ?? ''),
            'description'       => trim($_POST['description'] ?? ''),
            'localite'          => trim($_POST['localite'] ?? ''),
            'email_contact'     => trim($_POST['email_contact'] ?? ''),
            'telephone_contact' => trim($_POST['telephone_contact'] ?? 'Non renseigné')
        ];

        if (empty($companyData['nom']) || empty($_POST['user_email'])) {
            Template::render('companies/form.html.twig', [
                'error' => 'Le nom de l\'entreprise et l\'email du compte sont obligatoires.'
            ]);
            return;
        }

        $companyId = $this->companyModel->create($companyData);

        if ($companyId) {
            $userModel = new User();
            $userData = [
                'nom'        => trim($_POST['user_nom'] ?? $companyData['nom']),
                'prenom'     => trim($_POST['user_prenom'] ?? 'Contact'),
                'email'      => trim($_POST['user_email']),
                'password'   => $_POST['user_password'] ?? 'ChangeMe123!',
                'role'       => 'entreprise',
                'company_id' => $companyId, 
                'is_active'  => 1
            ];
            $userModel->create($userData);
            header('Location: /entreprises?success=created');
        } else {
            Template::render('companies/form.html.twig', ['error' => 'Erreur lors de la création.']);
        }
        exit;
    }

    public function dashboard(): void
    {
        Auth::refresh();
        AuthMiddleware::requireRole('entreprise');
        $user = Auth::user();
        $company = $this->companyModel->find($user['company_id'] ?? 0); 
        if (!$company) {
            Template::render('auth/waiting_validation.html.twig');
            return;
        }
        Template::render('company_account/dashboard.html.twig', ['company' => $company]);
    }

    public function index(): void
    {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $search = trim($_GET['nom'] ?? ''); 
        $where = !empty($search) ? "nom LIKE ?" : "";
        $params = !empty($search) ? ["%$search%"] : [];
        $companiesPaginator = $this->companyModel->paginate($page, 12, $where, $params);
        Template::render('companies/index.html.twig', [
            'companies' => $companiesPaginator,
            'filters'   => ['nom' => $search]
        ]);
    }

    public function show(string $id): void
    {
        $company = $this->companyModel->find((int)$id);
        if (!$company) { 
            http_response_code(404);
            Template::render('errors/404.html.twig');
            return; 
        }
        $offerResult = (new Offer())->search(1, 100, '', 0, (int)$id);
        $reviews = (new Review())->findByCompany((int)$id);
        $hasReviewed = Auth::check() && (new Review())->hasAlreadyReviewed((int)$id, Auth::id());

        Template::render('companies/show.html.twig', [
            'company' => $company,
            'offers'  => $offerResult['data'] ?? [],
            'reviews' => $reviews,
            'hasReviewed' => $hasReviewed
        ]);
    }

    public function edit(string $id = null): void
    {
        Auth::refresh();
        AuthMiddleware::requireRole('entreprise', 'admin', 'pilote');
        
        $targetId = $id ?: Auth::user()['company_id'];
        $company = $this->companyModel->find((int)$targetId);
        
        if (!$company) {
            header('Location: /entreprises?error=not_found');
            exit;
        }

        Template::render('companies/form.html.twig', ['company' => $company]);
    }

    public function update(string $id = null): void
    {
        AuthMiddleware::requireRole('entreprise', 'admin', 'pilote');
        AuthMiddleware::verifyCsrf();
        
        $targetId = $id ?: ($_POST['company_id'] ?? Auth::user()['company_id']);

        $data = [
            'nom'               => trim($_POST['nom'] ?? ''),
            'description'       => trim($_POST['description'] ?? ''),
            'localite'          => trim($_POST['localite'] ?? ''),
            'email_contact'     => trim($_POST['email_contact'] ?? ''),
            'telephone_contact' => trim($_POST['telephone_contact'] ?? '')
        ];

        if ($this->companyModel->update((int)$targetId, $data)) {
            if (Auth::hasRole('admin', 'pilote')) {
                header('Location: /entreprises/' . $targetId . '?success=1');
            } else {
                header('Location: /dashboard/entreprise?success=1');
            }
        } else {
            $company = $this->companyModel->find((int)$targetId);
            Template::render('companies/form.html.twig', [
                'company' => $company,
                'error' => 'Erreur lors de la mise à jour.'
            ]);
        }
        exit;
    }

    public function destroy(string $id): void
    {
        AuthMiddleware::requireRole('admin', 'pilote');
        $this->companyModel->delete((int)$id);
        header('Location: /entreprises?deleted=1');
        exit;
    }

    public function myOffers(): void
    {
        Auth::refresh();
        AuthMiddleware::requireRole('entreprise');
        $companyId = Auth::user()['company_id'] ?? 0;
        $offerResult = (new Offer())->search(1, 100, '', 0, $companyId);
        Template::render('company_account/my_offers.html.twig', [
            'offers' => $offerResult['data'],
            'company' => $this->companyModel->find($companyId)
        ]);
    }

    public function applications(): void
    {
        Auth::refresh();
        AuthMiddleware::requireRole('entreprise');
        $companyId = Auth::user()['company_id'] ?? 0;
        $candidatures = (new Application())->findByCompany($companyId);
        Template::render('company_account/applications.html.twig', [
            'candidatures' => $candidatures,
            'company' => $this->companyModel->find($companyId)
        ]);
    }

    public function updateApplicationStatus(string $id): void
    {
        Auth::refresh();
        AuthMiddleware::requireRole('entreprise');
        AuthMiddleware::verifyCsrf();
        $status = $_POST['statut'] ?? '';
        $validStatuses = ['en_attente', 'acceptee', 'refusee'];
        if (!in_array($status, $validStatuses)) {
            header('Location: /entreprise/candidatures?error=invalid_status');
            exit;
        }
        $applicationModel = new Application();
        if ($applicationModel->updateStatus((int)$id, $status)) {
            header('Location: /entreprise/candidatures?success=status_updated');
        } else {
            header('Location: /entreprise/candidatures?error=update_failed');
        }
        exit;
    }

    public function editCompany(): void { $this->edit(); }
    public function updateCompany(): void { $this->update(); }

    /**
     * Gère la soumission d'un avis sur une entreprise
     */
    public function review(string $id): void
    {
        Auth::refresh();
        AuthMiddleware::requireRole('admin', 'pilote');
        AuthMiddleware::verifyCsrf();

        $note = (int)($_POST['note'] ?? 0);
        $commentaire = trim($_POST['commentaire'] ?? '');

        if ($note < 1 || $note > 5) {
            header("Location: /entreprises/{$id}?error=note_invalide");
            exit;
        }

        $reviewModel = new Review();
        $userId = Auth::id();

        if ($reviewModel->hasAlreadyReviewed((int)$id, $userId)) {
            header("Location: /entreprises/{$id}?error=already_reviewed");
            exit;
        }

        // CORRECTION ICI : On utilise ta méthode review() au lieu de create()
        $reviewModel->review([
            'entreprise_id'  => (int)$id,
            'utilisateur_id' => $userId,
            'note'           => $note,
            'commentaire'    => $commentaire
        ]);

        header("Location: /entreprises/{$id}?success=avis_publie");
        exit;
    }
}
