<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Template;
use App\Models\User;
use App\Models\CompanyAccount;
use App\Middleware\AuthMiddleware;

class AuthController
{
    public function loginForm(): void
    {
        Template::render('auth/login.html.twig');
    }

    public function login(): void
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if ($user && password_verify($password, $user['mot_de_passe'])) {
            Auth::set($user);
            
            match($user['role']) {
                'admin'      => header('Location: /dashboard/admin'),
                'pilote'     => header('Location: /dashboard/pilote'),
                'entreprise' => header('Location: /dashboard/entreprise'),
                'visiteur'   => header('Location: /inscription/en-attente'), 
                default      => header('Location: /dashboard'),
            };
            exit;
        }

        Template::render('auth/login.html.twig', ['error' => 'Identifiants invalides']);
    }

    public function logout(): void
    {
        Auth::logout();
        header('Location: /connexion');
        exit;
    }

    /**
     * Affiche la page de modification du profil
     */
    public function profile(): void
    {
        AuthMiddleware::check();
        
        $userModel = new User();
        $user = $userModel->find(Auth::id());

        Template::render('profile.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * Traite la mise à jour du profil
     */
    public function updateProfile(): void
    {
        AuthMiddleware::check();

        $userModel = new User();
        $currentUserId = Auth::id();
        $user = $userModel->find($currentUserId);

        $data = [
            'nom'       => trim($_POST['nom'] ?? ''),
            'prenom'    => trim($_POST['prenom'] ?? ''),
            'email'     => trim($_POST['email'] ?? ''),
            'telephone' => trim($_POST['telephone'] ?? ''), // Récupération du champ téléphone
            'role'      => $user['role']
        ];

        // Sécurité pour les admins (pas de téléphone en BDD)
        if ($user['role'] === 'admin') {
            $data['telephone'] = null;
        }

        // Validation (le 'false' indique que le mot de passe n'est pas obligatoire ici)
        $errors = User::validateData($data, false);

        if ($errors) {
            Template::render('profile.html.twig', [
                'errors' => $errors,
                'user'   => array_merge($user, $data),
                'old'    => $data
            ]);
            return;
        }

        // Hachage du mot de passe seulement s'il est renseigné
        if (!empty($_POST['password'])) {
            $data['mot_de_passe'] = password_hash($_POST['password'], PASSWORD_BCRYPT);
        }

        $userModel->update($currentUserId, $data);

        // On rafraîchit la session pour mettre à jour le nom/prénom dans l'interface
        Auth::set($userModel->find($currentUserId));

        header('Location: /profil?updated=1');
        exit;
    }

    public function registerForm(): void
    {
        $userModel = new User();
        Template::render('auth/register.html.twig', [
            'pilotes' => $userModel->getPilotes()
        ]);
    }

    public function register(): void
    {
        $userModel = new User();
        $data = [
            'nom'               => trim($_POST['nom'] ?? ''),
            'prenom'            => trim($_POST['prenom'] ?? ''),
            'email'             => trim($_POST['email'] ?? ''),
            'telephone'         => trim($_POST['telephone'] ?? ''), 
            'password'          => $_POST['password'] ?? '', 
            'role'              => $_POST['role'] ?? 'etudiant',
            'pilote_id'         => $_POST['pilote_id'] ?: null,
        ];

        $passwordConfirm = $_POST['password_confirm'] ?? '';
        $errors = User::validateData($data, true);

        if ($data['password'] !== $passwordConfirm) {
            $errors[] = "Les deux mots de passe ne correspondent pas.";
        }

        if ($errors) {
            Template::render('auth/register.html.twig', [
                'errors' => $errors,
                'old' => $data,
                'pilotes' => $userModel->getPilotes()
            ]);
            return;
        }

        $userModel->create($data);
        header('Location: /connexion?registered=1');
        exit;
    }

    public function registerCompanyForm(): void
    {
        Template::render('auth/register_company.html.twig');
    }

    public function registerCompany(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /inscription/entreprise');
            exit;
        }

        $userModel = new User();
        $caModel = new CompanyAccount();

        $userData = [
            'nom'               => trim($_POST['nom'] ?? ''),
            'prenom'            => trim($_POST['prenom'] ?? ''),
            'email'             => trim($_POST['email'] ?? ''),
            'telephone'         => trim($_POST['telephone'] ?? ''), 
            'password'          => $_POST['password'] ?? '',
            'role'              => 'visiteur',
            'pilote_id'         => null
        ];

        $passwordConfirm = $_POST['password_confirm'] ?? '';
        $errors = User::validateData($userData, true);

        if ($userData['password'] !== $passwordConfirm) {
            $errors[] = "Les deux mots de passe ne correspondent pas.";
        }

        if ($errors) {
            Template::render('auth/register_company.html.twig', [
                'errors' => $errors,
                'old' => $_POST 
            ]);
            return;
        }

        $userId = $userModel->create($userData);

        $filename = null;
        if (isset($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['document']['name'], PATHINFO_EXTENSION);
            $filename = 'req_' . $userId . '_' . time() . '.' . $ext;
            $dest = dirname(__DIR__, 2) . '/storage/uploads/company_requests/' . $filename;
            move_uploaded_file($_FILES['document']['tmp_name'], $dest);
        }

        $caModel->createRequest([
            'user_id'                => $userId,
            'document_path'          => $filename,
            'temp_company_name'      => trim($_POST['company_name'] ?? ''),
            'temp_company_desc'      => trim($_POST['company_description'] ?? ''),
            'temp_company_location'  => trim($_POST['company_location'] ?? ''),
            'temp_company_phone'     => trim($_POST['company_phone'] ?? 'Non renseigné')
        ]);

        header('Location: /inscription/en-attente');
        exit;
    }

    public function pending(): void
    {
        if (Auth::role() !== 'visiteur') {
            header('Location: /');
            exit;
        }

        Template::render('auth/waiting_validation.html.twig');
    }

    public function deleteAccountForm(): void
    {
        AuthMiddleware::requireRole('etudiant', 'pilote', 'admin', 'entreprise');
        Template::render('profile/delete_account.html.twig');
    }

    public function deleteAccount(): void
    {
        AuthMiddleware::requireRole('etudiant', 'pilote', 'admin', 'entreprise');

        $passwordInput = $_POST['password'] ?? '';
        $confirmWord = $_POST['confirm'] ?? '';
        
        $userModel = new User();
        $user = $userModel->find(Auth::id());

        if ($confirmWord !== 'SUPPRIMER') {
            Template::render('profile/delete_account.html.twig', [
                'error' => 'Vous devez saisir le mot "SUPPRIMER" en majuscules pour confirmer.'
            ]);
            return;
        }

        if (!$user || !password_verify($passwordInput, $user['mot_de_passe'])) {
            Template::render('profile/delete_account.html.twig', [
                'error' => 'Le mot de passe saisi est incorrect.'
            ]);
            return;
        }

        $userModel->delete(Auth::id());
        Auth::logout();

        header('Location: /connexion?account_deleted=1');
        exit;
    }
}
