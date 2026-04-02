<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Application extends Model
{
    protected string $table = 'application';

    public function create(array $data): bool
    {
        $sql = "INSERT INTO {$this->table} (offre_id, etudiant_id, statut, lettre_motivation, cv_path, date_candidature) 
                VALUES (:offre_id, :etudiant_id, :statut, :lettre_motivation, :cv_path, NOW())";
        
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([
            'offre_id'          => $data['offre_id'],
            'etudiant_id'       => $data['etudiant_id'],
            'statut'            => $data['statut'] ?? 'en_attente',
            'lettre_motivation' => $data['lettre_motivation'] ?? null,
            'cv_path'           => $data['cv_path'] ?? null
        ]);
    }

    public function findByCompany(int $companyId): array
    {
        $sql = "SELECT 
                    a.*, 
                    u.nom AS etudiant_nom, 
                    u.prenom AS etudiant_prenom, 
                    u.email AS etudiant_email,
                    o.titre AS offre_titre,
                    c.nom AS entreprise_nom 
                FROM application a
                JOIN user u ON a.etudiant_id = u.id
                JOIN offer o ON a.offre_id = o.id
                JOIN company c ON o.entreprise_id = c.id
                WHERE o.entreprise_id = ?
                ORDER BY a.date_candidature DESC";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$companyId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function byStudent(int $userId): array
    {
        $sql = "SELECT a.*, i.titre AS offre_titre, c.nom AS entreprise_nom
                FROM {$this->table} a
                JOIN offer i ON a.offre_id = i.id
                JOIN company c ON i.entreprise_id = c.id
                WHERE a.etudiant_id = ?
                ORDER BY a.date_candidature DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les candidatures des étudiants rattachés à un pilote spécifique
     */
    public function byPilote(int $piloteId): array
    {
        $sql = "SELECT a.*, i.titre AS offre_titre, u.nom AS etudiant_nom, u.prenom AS etudiant_prenom, c.nom AS entreprise_nom
                FROM {$this->table} a
                JOIN offer i ON a.offre_id = i.id
                JOIN user u ON a.etudiant_id = u.id
                JOIN company c ON i.entreprise_id = c.id
                WHERE u.pilote_id = ?
                ORDER BY a.date_candidature DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$piloteId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function exists(int $offerId, int $userId): bool
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} WHERE offre_id = ? AND etudiant_id = ?");
        $stmt->execute([$offerId, $userId]);
        return $stmt->fetchColumn() > 0;
    }

    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET statut = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    // --- NOUVELLES METHODES DE FILTRAGE PILOTE ---
    public function countByPilote(int $piloteId): int
    {
        $sql = "SELECT COUNT(a.id) FROM application a JOIN user u ON a.etudiant_id = u.id WHERE u.pilote_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$piloteId]);
        return (int)$stmt->fetchColumn();
    }

    public function countStagesFoundByPilote(int $piloteId): int
    {
        $sql = "SELECT COUNT(a.id) FROM application a JOIN user u ON a.etudiant_id = u.id WHERE u.pilote_id = ? AND a.statut = 'acceptee'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$piloteId]);
        return (int)$stmt->fetchColumn();
    }
}
