<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Template;
use App\Models\Application;
use App\Models\Offer;
use App\Middleware\AuthMiddleware;

class ApplicationController
{
    /** GET /candidatures */
    public function index(): void
    {
        AuthMiddleware::requireRole('etudiant');
        $apps = (new Application())->byStudent(Auth::id());
        Template::render('applications/index.html.twig', ['candidatures' => $apps]);
    }

    /** POST /offres/{id}/postuler */
    public function store(string $offerId): void
    {
        AuthMiddleware::requireRole('etudiant');
        AuthMiddleware::verifyCsrf();

        $offerId    = (int)$offerId;
        $etudiantId = Auth::id();
        $appModel   = new Application();

        // Vérifier que l'offre existe
        $offer = (new Offer())->find($offerId);
        if (!$offer) {
            header('Location: /offres');
            exit;
        }

        // Vérifier la candidature en double
        if ($appModel->exists($offerId, $etudiantId)) {
            header("Location: /offres/{$offerId}?error=already_applied");
            exit;
        }

        // Validation LM
        $lm = trim($_POST['lettre_motivation'] ?? '');
        if (strlen($lm) < 50) {
            header("Location: /offres/{$offerId}?error=lm_too_short");
            exit;
        }

        // Upload CV
        $cvPath = $this->handleCvUpload($etudiantId);
        if ($cvPath === null) {
            header("Location: /offres/{$offerId}?error=cv_invalid");
            exit;
        }

        // Enregistrement
        $appModel->create([
            'offre_id'          => $offerId,
            'etudiant_id'       => $etudiantId,
            'lettre_motivation' => $lm,
            'cv_path'           => $cvPath,
            'statut'            => 'en_attente',
        ]);

        header("Location: /candidatures?success=1");
        exit;
    }

    /** GET /uploads/{filename} - téléchargement sécurisé du CV */
  public function downloadCv(string $filename): void
{
    AuthMiddleware::requireAuth();

    $filename = basename($filename);
    $cfg      = require dirname(__DIR__, 2) . '/config/app.php';
    $filepath = $cfg['upload_dir'] . $filename;

    if (!file_exists($filepath)) {
        http_response_code(404);
        echo 'Fichier introuvable.';
        return;
    }

    $role = Auth::role();
    $db = \App\Core\Database::getInstance();

    if ($role === 'etudiant') {
        $app = $db->prepare("SELECT id FROM application WHERE cv_path = ? AND etudiant_id = ?");
        $app->execute([$filename, Auth::id()]);
        if (!$app->fetch()) { $this->deny(); }

    } elseif ($role === 'pilote') {
        $app = $db->prepare("SELECT a.id FROM application a JOIN user u ON u.id = a.etudiant_id WHERE a.cv_path = ? AND u.pilote_id = ?");
        $app->execute([$filename, Auth::id()]);
        if (!$app->fetch()) { $this->deny(); }

    } elseif ($role === 'entreprise') {
            // On vérifie que le CV appartient à une candidature reçue pour une offre 
            // de l'entreprise rattachée à l'utilisateur (via company_id)
            $app = $db->prepare("
                SELECT a.id 
                FROM application a 
                JOIN offer o ON o.id = a.offre_id 
                JOIN user u ON u.company_id = o.entreprise_id
                WHERE a.cv_path = ? AND u.id = ?
            ");
            $app->execute([$filename, Auth::id()]);
            
            if (!$app->fetch()) {
                $this->deny();
            }

    } elseif ($role !== 'admin') {
        $this->deny();
    }

    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="' . $filename . '"');
    header('Content-Length: ' . filesize($filepath));
    readfile($filepath);
    exit;
}

// Petite fonction helper pour éviter la répétition
private function deny(): void {
    http_response_code(403);
    echo 'Accès refusé.';
    exit;
}

    private function handleCvUpload(int $userId): ?string
    {
        $cfg    = require dirname(__DIR__, 2) . '/config/app.php';
        $file   = $_FILES['cv'] ?? null;

        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        if ($file['size'] > $cfg['upload_max_mb'] * 1024 * 1024) {
            return null;
        }

        $finfo    = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        $allowed  = ['application/pdf'];

        if (!in_array($mimeType, $allowed, true)) {
            return null;
        }

        $filename = sprintf('cv_%d_%s.pdf', $userId, bin2hex(random_bytes(8)));
        $dest     = $cfg['upload_dir'] . $filename;

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            return null;
        }

        return $filename;
    }
}
