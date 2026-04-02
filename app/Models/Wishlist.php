<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Wishlist extends Model
{
    protected string $table = 'wishlist';

    public function paginateByStudent(int $etudiantId, int $page, int $perPage = 12): array
    {
        $offset = ($page - 1) * $perPage;

        $sql = "
            SELECT w.*, o.id as offre_id, o.titre, o.remuneration, 
                   c.nom AS entreprise_nom
            FROM wishlist w
            JOIN offer o   ON o.id = w.offre_id
            JOIN company c ON c.id = o.entreprise_id
            WHERE w.etudiant_id = ?
            ORDER BY w.id DESC
            LIMIT $perPage OFFSET $offset
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$etudiantId]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($items as &$item) {
            $item['skills'] = $this->getSkillsByOffer($item['offre_id']);
        }

        $totalStmt = $this->db->prepare("SELECT COUNT(*) FROM wishlist WHERE etudiant_id = ?");
        $totalStmt->execute([$etudiantId]);
        $totalCount = (int)$totalStmt->fetchColumn();

        return [
            'data'    => $items,
            'total'   => $totalCount,
            'pages'   => ceil($totalCount / $perPage),
            'current' => $page
        ];
    }

    private function getSkillsByOffer(int $offerId): array
    {
        $stmt = $this->db->prepare("
            SELECT s.nom 
            FROM skill s 
            JOIN offer_skill os ON s.id = os.skill_id 
            WHERE os.offre_id = ?
        ");
        $stmt->execute([$offerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function add(int $etudiantId, int $offreId): bool
    {
        if ($this->has($etudiantId, $offreId)) return false;
        $stmt = $this->db->prepare("INSERT INTO wishlist (etudiant_id, offre_id) VALUES (?, ?)");
        return $stmt->execute([$etudiantId, $offreId]);
    }

    public function remove(int $etudiantId, int $offreId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM wishlist WHERE etudiant_id = ? AND offre_id = ?");
        return $stmt->execute([$etudiantId, $offreId]);
    }

    public function has(int $etudiantId, int $offreId): bool
    {
        $stmt = $this->db->prepare("SELECT id FROM wishlist WHERE etudiant_id = ? AND offre_id = ?");
        $stmt->execute([$etudiantId, $offreId]);
        return (bool) $stmt->fetchColumn();
    }
}
