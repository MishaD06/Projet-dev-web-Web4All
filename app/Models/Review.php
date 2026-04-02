<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Review extends Model
{
    protected string $table = 'review';

    /**
     * Insère un nouvel avis
     */
    public function review(array $data): bool
    {
        $sql = "INSERT INTO {$this->table} 
                (entreprise_id, utilisateur_id, note, commentaire) 
                VALUES (?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            (int)$data['entreprise_id'],
            (int)$data['utilisateur_id'],
            (int)$data['note'],
            $data['commentaire'] ?? null
        ]);
    }

    /**
     * Vérifie si un utilisateur a déjà évalué cette entreprise
     */
    public function hasAlreadyReviewed(int $companyId, int $userId): bool
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE entreprise_id = ? AND utilisateur_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$companyId, $userId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * Récupère les avis d'une entreprise
     */
    public function findByCompany(int $companyId): array
    {
        $sql = "SELECT r.*, u.nom, u.prenom 
                FROM {$this->table} r
                JOIN user u ON r.utilisateur_id = u.id
                WHERE r.entreprise_id = ?
                ORDER BY r.date_review DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$companyId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
