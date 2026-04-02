<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Template;
use App\Models\CompanyAccount;
use App\Models\Company;
use App\Models\User;
use App\Models\PiloteAccount; 
use App\Models\StudentAccount;
use App\Middleware\AuthMiddleware;

class CompanyAccountController
{
    /**
     * Liste des demandes pour l'admin
     */
    public function adminIndex(): void
    {
        AuthMiddleware::requireRole('admin', 'pilote');
        
        $page = max(1, (int)($_GET['page'] ?? 1));
        $caModel = new CompanyAccount();
        
        $data = $caModel->allWithUser($page, 10);

        Template::render('company_account/admin_index.html.twig', [
            'accounts' => $data 
        ]);
    }

    /**
     * Détails d'une demande
     */
    public function adminShow(string $id): void
    {
        AuthMiddleware::requireRole('admin', 'pilote');
        
        $account = (new CompanyAccount())->findWithUser((int)$id);
        
        if (!$account) {
            header('Location: /admin/comptes-entreprises?error=not_found');
            exit;
        }

        Template::render('company_account/admin_show.html.twig', [
            'account' => $account
        ]);
    }

    /**
     * Approbation d'une entreprise
     */
    public function adminApprove(string $id): void
    {
        AuthMiddleware::requireRole('admin', 'pilote');
        AuthMiddleware::verifyCsrf();

        $caModel = new CompanyAccount();
        $ca = $caModel->findWithUser((int)$id);

        if ($ca && $ca['statut'] === 'pending') {
            // 1. Création de l'entreprise avec toutes les colonnes obligatoires
            $companyId = (new Company())->create([
                'nom' => $ca['temp_company_name'],
                'description' => $ca['temp_company_desc'],
                'email_contact' => $ca['email'],
                'localite' => $ca['temp_company_location'],
                // On passe le téléphone récupéré de la demande
                'telephone_contact' => $ca['temp_company_phone'] ?? 'Non renseigné'
            ]);

            if ($companyId) {
                // 2. Update de l'utilisateur (role + liaison)
                (new User())->updateCompanyLink((int)$ca['user_id'], (int)$companyId, 'entreprise');
                
                // 3. Update de la demande
                $caModel->approve((int)$id, (int)$companyId);
            }
        }

        header('Location: /admin/comptes-entreprises?approved=1');
        exit;
    }

    public function adminReject(string $id): void
    {
        AuthMiddleware::requireRole('admin', 'pilote');
        AuthMiddleware::verifyCsrf();

        (new CompanyAccount())->reject((int)$id);

        header('Location: /admin/comptes-entreprises?rejected=1');
        exit;
    }

    /**
     * Visionnage du document Kbis
     */
    public function viewDocument(string $filename): void
    {
        AuthMiddleware::requireRole('admin', 'pilote');

        $filePath = dirname(__DIR__, 2) . '/storage/uploads/company_requests/' . $filename;

        if (!file_exists($filePath) || !is_file($filePath)) {
            http_response_code(404);
            echo "Document introuvable sur le serveur.";
            exit;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filePath);
        finfo_close($finfo);

        header("Content-Type: " . $mimeType);
        header("Content-Disposition: inline; filename=\"" . $filename . "\"");
        header("Content-Length: " . filesize($filePath));

        readfile($filePath);
        exit;
    }

    // Gestion des pilotes

    /**
     * Liste des demandes pilotes pour l'admin (Historique complet)
     */
    public function adminPiloteIndex(): void
    {
        AuthMiddleware::requireRole('admin');

        $paModel = new PiloteAccount();
        // appeler la méthode d'historique complet
        $requests = $paModel->getAllWithUsers();

        Template::render('company_account/admin_pilote_index.html.twig', [
            'requests' => $requests
        ]);
    }

    /**
     * Approuve un compte pilote
     */
    public function adminPiloteApprove(string $id): void
    {
        AuthMiddleware::requireRole('admin');

        $paModel = new PiloteAccount();
        $userModel = new User();

        $request = $paModel->find((int)$id);

        if ($request && $request['statut'] === 'pending') {
            // 1. Passage du rôle visiteur à pilote
            $userModel->update((int)$request['user_id'], ['role' => 'pilote']);

            // 2. Mise à jour de la demande
            $paModel->update((int)$id, [
                'statut' => 'approved',
                'date_reponse' => date('Y-m-d H:i:s')
            ]);
        }

        header('Location: /admin/comptes-pilotes?approved=1');
        exit;
    }

    /**
     * Refuse un compte pilote
     */
    public function adminPiloteReject(string $id): void
    {
        AuthMiddleware::requireRole('admin');

        $paModel = new PiloteAccount();
        $request = $paModel->find((int)$id);

        if ($request && $request['statut'] === 'pending') {
            $paModel->update((int)$id, [
                'statut' => 'rejected',
                'date_reponse' => date('Y-m-d H:i:s')
            ]);
        }

        header('Location: /admin/comptes-pilotes?rejected=1');
        exit;
    }

    /**
     * Liste des demandes étudiants (Accessible Admin et Pilote)
     */
    public function adminStudentIndex(): void
    {
        AuthMiddleware::requireRole('admin', 'pilote');

        $saModel = new StudentAccount();

        // Si c'est un pilote, il ne voit que ses étudiants. Si c'est l'admin, il voit tout.
        if (Auth::role() === 'admin') {
            $requests = $saModel->getAllPending();
        } else {
            $requests = $saModel->getPendingByPilote((int)Auth::id());
        }

        Template::render('company_account/admin_student_index.html.twig', [
            'requests' => $requests
        ]);
    }
}
