<?php

namespace App\Models;

use App\Core\Model; 
use PDO;

class Company extends Model
{
    protected string $table = 'company';

    /**
     * Récupère une entreprise par son ID
     */
    public function find(int $id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $res ?: null;
    }

    /**
     * Trouve l'entreprise liée à un utilisateur spécifique
     */
    public function findByUser(int $userId): ?array
    {
        $sql = "SELECT c.* FROM {$this->table} c 
                JOIN user u ON u.company_id = c.id 
                WHERE u.id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $res ?: null;
    }

    /**
     * CRÉATION DYNAMIQUE
     * S'adapte automatiquement aux colonnes envoyées par le contrôleur
     */
    public function create(array $data): int|bool
    {
        if (empty($data)) return false;

        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute(array_values($data));

        return $result ? (int)$this->db->lastInsertId() : false;
    }

    /**
     * MISE À JOUR DYNAMIQUE
     * Remplacée par une version plus flexible pour éviter les erreurs de colonnes manquantes
     */
    public function update(int $id, array $data): bool
    {
        if (empty($data)) return false;

        $fields = [];
        $params = [];

        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $params[] = $value;
        }

        $params[] = $id;
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * SUPPRESSION
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}
