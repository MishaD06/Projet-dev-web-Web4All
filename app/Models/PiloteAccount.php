<?php

namespace App\Models;

use App\Core\Model;

class PiloteAccount extends Model
{
    protected string $table = 'pilote_account';

    /**
     * Crée une demande de validation pour un pilote
     */
    public function createRequest(int $userId): bool
    {
        $sql = "INSERT INTO {$this->table} (user_id, statut, date_demande) VALUES (?, 'pending', NOW())";
        return $this->db->prepare($sql)->execute([$userId]);
    }

    /**
     * Récupère la demande d'un utilisateur spécifique
     */
    public function findByUser(int $userId): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ? ORDER BY date_demande DESC LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Récupère les demandes en attente avec les infos de l'utilisateur
     */
    public function getPendingWithUsers(): array
    {
        $sql = "SELECT pa.*, u.nom, u.prenom, u.email, u.telephone 
                FROM {$this->table} pa
                JOIN user u ON pa.user_id = u.id
                WHERE pa.statut = 'pending'
                ORDER BY pa.date_demande DESC";
        
        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Récupère toutes les demandes (Historique complet)
     * Trie les pending en haut, puis par date décroissante
     */
    public function getAllWithUsers(): array
    {
        $sql = "SELECT pa.*, u.nom, u.prenom, u.email, u.telephone 
                FROM {$this->table} pa
                JOIN user u ON pa.user_id = u.id
                ORDER BY 
                    CASE WHEN pa.statut = 'pending' THEN 1 ELSE 2 END, 
                    pa.date_demande DESC";
        
        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Permet de mettre à jour le statut (Approuver/Refuser)
     */
    public function update(int $id, array $data): bool
    {
        $fields = [];
        $values = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $values[] = $value;
        }
        $values[] = $id;

        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = ?";
        return $this->db->prepare($sql)->execute($values);
    }
}
