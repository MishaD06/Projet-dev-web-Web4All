<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class CompanyAccount extends Model
{
    protected string $table = 'company_account';

    /**
     * 1. POUR L'UTILISATEUR : Créer la demande
     */
    public function createRequest(array $data): bool
    {
        $sql = "INSERT INTO company_account (
                    user_id, 
                    document_path, 
                    statut, 
                    temp_company_name, 
                    temp_company_desc, 
                    temp_company_location, 
                    temp_company_phone,
                    date_demande
                ) VALUES (?, ?, 'pending', ?, ?, ?, ?, NOW())";
        
        return $this->db->prepare($sql)->execute([
            $data['user_id'],
            $data['document_path'],
            $data['temp_company_name'],
            $data['temp_company_desc'],
            $data['temp_company_location'],
            $data['temp_company_phone'] ?? 'Non renseigné'
        ]);
    }

    /**
     * 2. POUR L'ADMIN : Lister toutes les demandes avec les infos du user
     */
    public function allWithUser(int $page, int $perPage): array
    {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT ca.*, u.nom as user_nom, u.prenom as user_prenom, u.email 
                FROM company_account ca 
                LEFT JOIN user u ON ca.user_id = u.id 
                ORDER BY ca.date_demande DESC 
                LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int)$perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total = $this->db->query("SELECT COUNT(*) FROM company_account")->fetchColumn();

        return [
            'items'      => $items,
            'total'      => (int)$total,
            'last_page' => (int) ceil($total / $perPage),
            'current_page' => $page
        ];
    }

    /**
     * 3. POUR L'ADMIN : Voir une demande précise
     */
    public function findWithUser(int $id): ?array
    {
        $sql = "SELECT ca.*, u.nom, u.prenom, u.email 
                FROM company_account ca 
                LEFT JOIN user u ON ca.user_id = u.id 
                WHERE ca.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([(int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * 4. POUR LE DASHBOARD : Trouver la demande d'un utilisateur
     */
    public function findByUser(int $userId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM company_account WHERE user_id = ? ORDER BY date_demande DESC LIMIT 1");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * 5. ACTIONS ADMIN : Approuver / Refuser
     */
    public function approve(int $id, int $companyId): bool
    {
        $stmt = $this->db->prepare("UPDATE company_account SET statut = 'approved', company_id = ?, date_reponse = NOW() WHERE id = ?");
        return $stmt->execute([$companyId, $id]);
    }

    public function reject(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE company_account SET statut = 'rejected', date_reponse = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
