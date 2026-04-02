<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Template;
use App\Models\User;
use App\Models\Application;
use App\Models\StudentAccount; 
use App\Middleware\AuthMiddleware;

class UserController
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function index(): void
    {
        if (Auth::role() !== 'admin' && Auth::role() !== 'pilote') {
            $this->abort403();
            return;
        }

        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 12; 
        $q = trim($_GET['q'] ?? '');
        $role = $_GET['role'] ?? '';
        
        $piloteId = (Auth::role() === 'pilote') ? Auth::id() : null;

        $result = $this->userModel->search($page, $perPage, $q, $role, $piloteId);
        
        $appModel = new Application();
        if (isset($result['data'])) {
            foreach ($result['data'] as &$user) {
                if ($user['role'] === 'etudiant') {
                    $userApps = $appModel->byStudent((int)$user['id']);
                    $user['last_application'] = !empty($userApps) ? $userApps[0] : null;
                }
            }
            unset($user); 
        }

        $stats = null;
        if (Auth::role() === 'pilote') {
            $stats = [
                'nb_etudiants'    => $this->userModel->countStudentsByPilote($piloteId),
                'nb_candidatures' => $appModel->countByPilote($piloteId),
                'nb_stages'        => $appModel->countStagesFoundByPilote($piloteId),
                'nb_recherche'    => $this->userModel->countStudentsSearchingByPilote($piloteId)
            ];
        }
        
        Template::render('users/index.html.twig', [
            'users'   => $result,
            'filters' => compact('q', 'role'),
            'stats'   => $stats,
            'pilotes' => (Auth::role() === 'admin') ? $this->userModel->getPilotes() : []
        ]);
    }

    public function show(string $id): void
    {
        if (Auth::role() !== 'admin' && Auth::role() !== 'pilote') { $this->abort403(); return; }
        $user = $this->userModel->find((int)$id);
        if (!$user) { $this->abort404(); return; }
        if (Auth::role() === 'pilote') {
            $isHisStudent = ($user['role'] === 'etudiant' && $user['pilote_id'] == Auth::id());
            $isSelf = ($user['id'] == Auth::id());
            if (!$isHisStudent && !$isSelf) {
                header('Location: /admin/utilisateurs?error=access_denied');
                exit;
            }
        }
        $candidatures = ($user['role'] === 'etudiant') ? (new Application())->byStudent((int)$id) : [];
        Template::render('users/show.html.twig', ['user' => $user, 'candidatures' => $candidatures]);
    }

    public function create(): void
    {
        if (Auth::role() !== 'admin' && Auth::role() !== 'pilote') { $this->abort403(); return; }
        Template::render('users/form.html.twig', [
            'user' => null, 
            'pilotes' => $this->userModel->getPilotes(),
            'auth_role' => Auth::role() 
        ]);
    }

    public function store(): void
    {
        if (Auth::role() !== 'admin' && Auth::role() !== 'pilote') { $this->abort403(); return; }
        AuthMiddleware::verifyCsrf();
        $data = $this->extractFormData();
        
        if (Auth::role() === 'pilote') { 
            $data['role'] = 'etudiant'; 
            $data['pilote_id'] = Auth::id(); 
        }
        
        $errors = User::validateData($data, true, null);
        
        if ($errors) {
            Template::render('users/form.html.twig', [
                'user' => null, 
                'pilotes' => $this->userModel->getPilotes(), 
                'errors' => $errors, 
                'old' => $data,
                'auth_role' => Auth::role()
            ]);
            return;
        }

        if ($this->userModel->create($data)) {
            header('Location: /admin/utilisateurs?created=1');
            exit;
        } else {
            Template::render('users/form.html.twig', [
                'user' => null, 
                'pilotes' => $this->userModel->getPilotes(), 
                'errors' => ["Une erreur est survenue lors de la création de l'utilisateur."], 
                'old' => $data,
                'auth_role' => Auth::role()
            ]);
        }
    }

    public function edit(string $id): void
    {
        if (Auth::role() !== 'admin' && Auth::role() !== 'pilote') { $this->abort403(); return; }
        $user = $this->userModel->find((int)$id);
        if (!$user) { $this->abort404(); return; }
        if (Auth::role() === 'pilote') {
            if ($user['role'] !== 'etudiant' || $user['pilote_id'] != Auth::id()) {
                header('Location: /admin/utilisateurs?error=unauthorized');
                exit;
            }
        }
        Template::render('users/form.html.twig', [
            'user' => $user, 
            'pilotes' => $this->userModel->getPilotes(),
            'auth_role' => Auth::role()
        ]);
    }

    public function update(string $id): void
    {
        if (Auth::role() !== 'admin' && Auth::role() !== 'pilote') { $this->abort403(); return; }
        AuthMiddleware::verifyCsrf();
        $user = $this->userModel->find((int)$id);
        if (!$user) { $this->abort404(); return; }
        $data = $this->extractFormData();
        
        if (Auth::role() === 'pilote') {
            if ($user['role'] !== 'etudiant' || $user['pilote_id'] != Auth::id()) { $this->abort403(); return; }
            $data['role'] = 'etudiant'; 
            $data['pilote_id'] = Auth::id();
        }
        
        $errors = User::validateData($data, false, (int)$id);
        
        if ($errors) {
            Template::render('users/form.html.twig', [
                'user' => $user, 
                'pilotes' => $this->userModel->getPilotes(), 
                'errors' => $errors, 
                'old' => $data,
                'auth_role' => Auth::role()
            ]);
            return;
        }

        if (!empty($data['password'])) { 
            $data['mot_de_passe'] = password_hash($data['password'], PASSWORD_BCRYPT); 
        }
        
        $this->userModel->update((int)$id, $data);
        header('Location: /admin/utilisateurs?updated=1');
        exit;
    }

    public function destroy(string $id): void
    {
        if (Auth::role() !== 'admin' && Auth::role() !== 'pilote') { $this->abort403(); return; }
        $user = $this->userModel->find((int)$id);
        if (!$user) { $this->abort404(); return; }
        if (Auth::role() === 'pilote' && $user['role'] !== 'etudiant') { $this->abort403(); return; }
        AuthMiddleware::verifyCsrf();
        $this->userModel->delete((int)$id);
        header('Location: /admin/utilisateurs?deleted=1');
        exit;
    }

    /**
     * Action pour approuver l'inscription d'un étudiant
     */
    public function approveStudent(): void
    {
        AuthMiddleware::requireRole('admin', 'pilote');
        // AuthMiddleware::verifyCsrf();

        $studentUserId = $_POST['user_id'] ?? null;

        if ($studentUserId) {
            $saModel = new StudentAccount();
            $saModel->approve((int)$studentUserId);
            $this->userModel->update((int)$studentUserId, ['role' => 'etudiant']);

            header('Location: /admin/comptes-etudiants?success=student_approved');
        } else {
            header('Location: /admin/comptes-etudiants?error=missing_id');
        }
        exit;
    }

    /**
     * Action pour refuser l'inscription d'un étudiant
     */
    public function rejectStudent(): void
    {
        AuthMiddleware::requireRole('admin', 'pilote');
        // AuthMiddleware::verifyCsrf();

        $studentUserId = $_POST['user_id'] ?? null;

        if ($studentUserId) {
            $saModel = new StudentAccount();
            // Au lieu de supprimer l'utilisateur, on marque simplement la demande comme refusée
            $saModel->reject((int)$studentUserId);

            header('Location: /admin/comptes-etudiants?success=student_rejected');
        } else {
            header('Location: /admin/comptes-etudiants?error=missing_id');
        }
        exit;
    }

    private function extractFormData(): array
    {
        $data = [
            'nom'        => trim($_POST['nom'] ?? ''),
            'prenom'     => trim($_POST['prenom'] ?? ''),
            'email'      => trim($_POST['email'] ?? ''),
            'telephone'  => trim($_POST['telephone'] ?? ''),
            'role'       => $_POST['role'] ?? 'etudiant',
            'pilote_id'  => !empty($_POST['pilote_id']) ? (int)$_POST['pilote_id'] : null,
            'password'   => $_POST['password'] ?? '',
        ];

        if ($data['role'] === 'admin') {
            $data['telephone'] = null;
        }

        return $data;
    }

    private function abort404(): void { http_response_code(404); Template::render('errors/404.html.twig'); }
    private function abort403(): void { http_response_code(403); Template::render('errors/403.html.twig'); }
}
