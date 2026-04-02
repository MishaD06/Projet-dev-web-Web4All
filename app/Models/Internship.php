<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Internship extends Model
{
    protected string $table = 'offer';

    public function findByCompany(int $companyId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE entreprise_id = ? ORDER BY date_publication DESC");
        $stmt->execute([$companyId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
