<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class StudentAccount
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Récupère toutes les demandes (pour l'Admin)
     */
    public function getAllPending(): array
    {
        return $this->fetchRequests();
    }

    /**
     * Récupère les demandes d'un pilote spécifique (pour le Pilote)

     */
    public function getPendingByPilote(int $piloteId): array
    {
        return $this->fetchRequests($piloteId);
    }

    /**
     * Méthode interne factorisée pour éviter la duplication de code SQL
     */
    private function fetchRequests(?int $piloteId = null): array
    {
        $sql = "SELECT sa.*, u.nom, u.prenom, u.email, u.telephone, p.nom as pilote_nom 
                FROM student_account sa
                JOIN user u ON sa.user_id = u.id
                LEFT JOIN user p ON u.pilote_id = p.id";
        
        if ($piloteId) {
            $sql .= " WHERE u.pilote_id = :pilote_id";
        }
        
        $sql .= " ORDER BY sa.date_demande DESC";
        
        $stmt = $this->db->prepare($sql);
        if ($piloteId) {
            $stmt->bindValue(':pilote_id', $piloteId, PDO::PARAM_INT);
        }
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Approuve une demande
     */
    public function approve(int $userId): bool
    {
        $sql = "UPDATE student_account 
                SET statut = 'approved', date_reponse = NOW() 
                WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['user_id' => $userId]);
    }

    /**
     * Refuse une demande sans supprimer l'utilisateur
     */
    public function reject(int $userId): bool
    {
        $sql = "UPDATE student_account 
                SET statut = 'rejected', date_reponse = NOW() 
                WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['user_id' => $userId]);
    }

    /**
     * Vérifie si une demande en attente existe déjà
     */
    public function hasPendingRequest(int $userId): bool
    {
        $sql = "SELECT COUNT(*) FROM student_account WHERE user_id = :user_id AND statut = 'pending'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * Crée une nouvelle demande (Register)
     */
    public function createRequest(int $userId): bool
    {
        $sqlUser = "SELECT pilote_id FROM user WHERE id = :id";
        $stmtUser = $this->db->prepare($sqlUser);
        $stmtUser->execute(['id' => $userId]);
        $user = $stmtUser->fetch(PDO::FETCH_ASSOC);
        $piloteId = $user['pilote_id'] ?? null;

        $sql = "INSERT INTO student_account (user_id, pilote_id, statut, date_demande) 
                VALUES (:user_id, :pilote_id, 'pending', NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'user_id' => $userId,
            'pilote_id' => $piloteId
        ]);
    }
}
