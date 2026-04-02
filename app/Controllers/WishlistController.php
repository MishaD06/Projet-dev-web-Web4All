<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Models\Wishlist;
use App\Core\Template;
use App\Middleware\AuthMiddleware;

class WishlistController
{
    private function safeRedirectPath(string $fallback): string
    {
        $redirect = trim((string) ($_POST['redirect'] ?? ''));
        if ($redirect === '' || !str_starts_with($redirect, '/') || str_starts_with($redirect, '//')) return $fallback;
        return $redirect;
    }

    public function index(): void
    {
        AuthMiddleware::requireRole('etudiant');
        $page = max(1, (int)($_GET['page'] ?? 1));
        
        $result = (new Wishlist())->paginateByStudent(Auth::id(), $page, 12);

        Template::render('wishlist/index.html.twig', [
            'wishlist' => $result
        ]);
    }

    public function add(): void
    {
        AuthMiddleware::requireRole('etudiant');
        AuthMiddleware::verifyCsrf();
        $offerId = (int)($_POST['offre_id'] ?? 0);
        (new Wishlist())->add(Auth::id(), $offerId);
        header("Location: " . $this->safeRedirectPath("/offres/{$offerId}") . "?wishlist=added");
        exit;
    }

    public function remove(): void
    {
        AuthMiddleware::requireRole('etudiant');
        AuthMiddleware::verifyCsrf();
        $offerId = (int)($_POST['offre_id'] ?? 0);
        (new Wishlist())->remove(Auth::id(), $offerId);
        header("Location: /wishlist?wishlist=removed");
        exit;
    }
}
