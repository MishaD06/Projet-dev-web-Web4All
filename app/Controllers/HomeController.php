<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Template;
use App\Models\Offer;
use App\Models\Application;
use App\Models\User;
use App\Models\CompanyAccount;
use App\Models\PiloteAccount;
use App\Middleware\AuthMiddleware;

class HomeController
{
    /**
     * Aiguilleur central : évite l'accès refusé pour les comptes en attente
     */
    public function dashboard(): void
    {
        AuthMiddleware::requireAuth();
        $user = Auth::user();

        if ($user['role'] === 'visiteur') {
            // 1. On cherche d'abord si c'est une entreprise
            $caModel = new CompanyAccount();
            $request = $caModel->findByUser((int)$user['id']);

            // 2. Si on ne trouve rien, on cherche si c'est un pilote
            if (!$request) {
                $paModel = new PiloteAccount();
                $request = $paModel->findByUser((int)$user['id']);
            }

            // On affiche la page d'attente avec les données de la requête (entreprise ou pilote)
            Template::render('auth/waiting_validation.html.twig', ['account' => $request]);
            return;
        }

        switch ($user['role']) {
            case 'admin':
                $this->dashboardAdmin();
                break;
            case 'pilote':
                $this->dashboardPilote();
                break;
            case 'entreprise':
                header('Location: /dashboard/entreprise');
                exit;
            case 'etudiant':
                $this->dashboardStudent();
                break;
            default:
                header('Location: /');
                exit;
        }
    }
    
    public function index(): void
    {
        $offerModel = new Offer();
        $recent = $offerModel->search(1, 4)['data'];
        $db = Database::getInstance();

        $stats = [
            'offres'       => (int)$db->query("SELECT COUNT(*) FROM offer")->fetchColumn(),
            'entreprises'  => (int)$db->query("SELECT COUNT(*) FROM company")->fetchColumn(),
            'etudiants'    => (int)$db->query("SELECT COUNT(*) FROM user WHERE role='etudiant'")->fetchColumn(),
            'candidatures' => (int)$db->query("SELECT COUNT(*) FROM application")->fetchColumn(),
        ];

        Template::render('home/index.html.twig', ['recent_offers' => $recent, 'stats' => $stats]);
    }

    public function dashboardStudent(): void
    {
        AuthMiddleware::requireRole('etudiant');
        $userId = Auth::id();
        $appModel = new Application();
        $offerModel = new Offer();

        Template::render('dashboard/student.html.twig', [
            'candidatures'  => $appModel->byStudent($userId),
            'recent_offers' => $offerModel->search(1, 3)['data'],
        ]);
    }

    public function dashboardPilote(): void
    {
        AuthMiddleware::requireRole('pilote', 'admin');
        $piloteId = Auth::id();
        $userModel = new User();
        $appModel = new Application();
        
        $etudiants = $userModel->getStudentsByPilote($piloteId);
        $candidatures = $appModel->byPilote($piloteId);

        $candByStudent = [];
        foreach ($candidatures as $c) { 
            $candByStudent[$c['etudiant_id']][] = $c; 
        }

        Template::render('dashboard/pilote.html.twig', [
            'etudiants' => $etudiants,
            'cand_by_student' => $candByStudent,
            'stats' => [
                'etudiants'    => count($etudiants), 
                'candidatures' => count($candidatures),
                'acceptees'    => count(array_filter($candidatures, fn($c) => $c['statut'] === 'acceptee')),
                'en_recherche' => count(array_filter($etudiants, fn($e) => !isset($candByStudent[$e['id']]))),
            ],
        ]);
    }

    public function dashboardAdmin(): void
    {
        AuthMiddleware::requireRole('admin');
        $db = Database::getInstance();
        Template::render('dashboard/admin.html.twig', [
            'stats' => [
                'users'        => (int)$db->query("SELECT COUNT(*) FROM user")->fetchColumn(),
                'offres'       => (int)$db->query("SELECT COUNT(*) FROM offer")->fetchColumn(),
                'entreprises'  => (int)$db->query("SELECT COUNT(*) FROM company")->fetchColumn(),
                'candidatures' => (int)$db->query("SELECT COUNT(*) FROM application")->fetchColumn(),
            ],
            'recent_activity' => $db->query("SELECT 'candidature' AS type, a.date_candidature AS date, CONCAT(u.prenom, ' ', u.nom, ' → ', o.titre) AS label FROM application a JOIN user u ON u.id = a.etudiant_id JOIN offer o ON o.id = a.offre_id ORDER BY a.date_candidature DESC LIMIT 5")->fetchAll(),
        ]);
    }

    public function profile(): void
    {
        AuthMiddleware::requireAuth();
        
        $userModel = new User();
        $user = $userModel->find(Auth::id());

        Template::render('profile.html.twig', ['user' => $user]);
    }

    public function updateProfile(): void
    {
        AuthMiddleware::requireAuth();
        AuthMiddleware::verifyCsrf();
        
        $userModel = new User();
        $currentUser = $userModel->find(Auth::id());
        $currentId = (int)Auth::id(); 
        
        $data = [
            'nom'        => trim($_POST['nom'] ?? ''), 
            'prenom'     => trim($_POST['prenom'] ?? ''), 
            'email'      => trim($_POST['email'] ?? ''),
            'telephone'  => trim($_POST['telephone'] ?? ''),
            'password'   => $_POST['password'] ?? '',
            'role'       => $currentUser['role']
        ];

        $errors = User::validateData($data, false, $currentId);

        if ($errors) {
            Template::render('profile.html.twig', [
                'user'   => array_merge($currentUser, $data), 
                'errors' => $errors,
                'old'    => $data
            ]);
            return;
        }

        $updateData = [
            'nom'        => $data['nom'],
            'prenom'     => $data['prenom'],
            'email'      => $data['email'],
            'telephone'  => $data['telephone']
        ];

        if ($currentUser['role'] === 'admin') {
            $updateData['telephone'] = null;
        }

        if (!empty($data['password'])) {
            $updateData['mot_de_passe'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        $userModel->update($currentId, $updateData);
        
        Auth::refresh();
        
        header('Location: /profil?success=1');
        exit;
    }

    public function mentions(): void { Template::render('mentions.html.twig'); }
}
