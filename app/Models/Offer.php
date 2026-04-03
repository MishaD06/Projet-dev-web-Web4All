<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Offer extends Model
{
    protected string $table = 'offer'; 

    public function create(array $data, array $skillIds = []): int|bool
    {
        $sql = "INSERT INTO {$this->table} (titre, description, remuneration, date_publication, duree_mois, entreprise_id) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $success = $stmt->execute([
            $data['titre'], 
            $data['description'], 
            $data['remuneration'], 
            $data['date_publication'], 
            $data['duree_mois'], 
            $data['entreprise_id']
        ]);
        
        if ($success) {
            $id = (int)$this->db->lastInsertId();
            $this->syncSkills($id, $skillIds);
            return $id;
        }
        return false;
    }

    public function findFull(int $id): ?array
    {
        $sql = "SELECT o.*, c.nom as entreprise_nom, c.localite FROM {$this->table} o LEFT JOIN company c ON o.entreprise_id = c.id WHERE o.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $offer = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($offer) { 
            $offer['skills'] = $this->getSkills($id); 
        }
        return $offer ?: null;
    }

    public function search(int $page, int $perPage = 12, string $query = '', int $skillId = 0, int $companyId = 0): array
    {
        $offset = ($page - 1) * $perPage;
        $params = [];
        
        // Base de la requête avec jointures conditionnelles
        $sqlBase = "FROM {$this->table} o LEFT JOIN company c ON o.entreprise_id = c.id";
        
        if ($skillId > 0) {
            $sqlBase .= " JOIN offer_skill os ON o.id = os.offre_id";
        }

        $where = " WHERE 1=1";
        if ($query) {
            $where .= " AND (o.titre LIKE ? OR o.description LIKE ?)";
            $params[] = "%$query%"; 
            $params[] = "%$query%";
        }
        if ($companyId > 0) {
            $where .= " AND o.entreprise_id = ?";
            $params[] = $companyId;
        }
        if ($skillId > 0) {
            $where .= " AND os.skill_id = ?";
            $params[] = $skillId;
        }
        
        // Sélection des données
        $sql = "SELECT DISTINCT o.*, c.nom as entreprise_nom, c.localite $sqlBase $where ORDER BY o.id DESC LIMIT $perPage OFFSET $offset";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // On récupère les skills pour chaque offre pour l'affichage Twig
        foreach ($items as &$item) {
            $item['skills'] = $this->getSkills((int)$item['id']);
        }

        // Comptage pour la pagination
        $totalSql = "SELECT COUNT(DISTINCT o.id) $sqlBase $where";
        $totalStmt = $this->db->prepare($totalSql);
        $totalStmt->execute($params);
        $totalCount = (int)$totalStmt->fetchColumn();

        // On utilise current_page et last_page pour matcher avec pagination.html.twig
        return [
            'data'         => $items,
            'total'        => $totalCount,
            'per_page'     => $perPage,
            'current_page' => $page,
            'last_page'    => (int)ceil($totalCount / $perPage)
        ];
    }

    public function update(int $id, array $data, array $skillIds = []): bool
    {
        $sql = "UPDATE {$this->table} SET titre = ?, description = ?, remuneration = ?, date_publication = ?, duree_mois = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $success = $stmt->execute([
            $data['titre'], 
            $data['description'], 
            $data['remuneration'], 
            $data['date_publication'], 
            $data['duree_mois'], 
            $id
        ]);
        
        if ($success) {
            $this->syncSkills($id, $skillIds);
        }
        return $success;
    }

    private function syncSkills(int $offerId, array $skillIds): void
    {
        // Nettoyage des anciens liens
        $this->db->prepare("DELETE FROM offer_skill WHERE offre_id = ?")->execute([$offerId]);
        
        // Ajout des nouveaux liens
        if (!empty($skillIds)) {
            $stmt = $this->db->prepare("INSERT INTO offer_skill (offre_id, skill_id) VALUES (?, ?)");
            foreach ($skillIds as $sid) {
                $stmt->execute([$offerId, (int)$sid]);
            }
        }
    }

    private function getSkills(int $offerId): array
    {
        $stmt = $this->db->prepare("SELECT s.* FROM skill s JOIN offer_skill os ON s.id = os.skill_id WHERE os.offre_id = ?");
        $stmt->execute([$offerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function stats(): array
    {
        try {
            $sqlBase = "SELECT 
                (SELECT COUNT(*) FROM offer) as total_offres,
                (SELECT ROUND(AVG(duree_mois), 1) FROM offer) as avg_duree,
                (SELECT ROUND(COUNT(id) / NULLIF((SELECT COUNT(*) FROM offer), 0), 1) FROM application) as avg_candidatures";
            
            $base = $this->db->query($sqlBase)->fetch(PDO::FETCH_ASSOC);

            $sqlDist = "SELECT 
                SUM(CASE WHEN duree_mois <= 2 THEN 1 ELSE 0 END) as d2,
                SUM(CASE WHEN duree_mois = 3 THEN 1 ELSE 0 END) as d3,
                SUM(CASE WHEN duree_mois = 4 THEN 1 ELSE 0 END) as d4,
                SUM(CASE WHEN duree_mois = 5 THEN 1 ELSE 0 END) as d5,
                SUM(CASE WHEN duree_mois = 6 THEN 1 ELSE 0 END) as d6,
                SUM(CASE WHEN duree_mois > 6 THEN 1 ELSE 0 END) as d7
                FROM offer";
            $dist = $this->db->query($sqlDist)->fetch(PDO::FETCH_ASSOC);

            $sqlTop = "SELECT o.id, o.titre, c.nom as entreprise_nom, 
                    (SELECT COUNT(*) FROM wishlist w WHERE w.offre_id = o.id) as nb_wishlist,
                    (SELECT COUNT(*) FROM application a WHERE a.offre_id = o.id) as nb_candidatures
                    FROM offer o
                    JOIN company c ON o.entreprise_id = c.id
                    ORDER BY nb_wishlist DESC
                    LIMIT 5";
            $topList = $this->db->query($sqlTop)->fetchAll(PDO::FETCH_ASSOC);

            return [
                'total_offres'      => $base['total_offres'] ?? 0,
                'avg_candidatures'  => $base['avg_candidatures'] ?? 0,
                'avg_duree'         => $base['avg_duree'] ?? 0,
                'duration_dist'     => $dist,
                'top_wishlist'      => $topList[0] ?? null,
                'top_wishlist_list' => $topList
            ];
        } catch (\Exception $e) {
            return ['total_offres' => 0, 'avg_candidatures' => 0, 'avg_duree' => 0, 'duration_dist' => [], 'top_wishlist_list' => []];
        }
    }
}
