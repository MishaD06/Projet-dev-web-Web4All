<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class User extends Model
{
    protected string $table = 'user';

    /**
     * Valide les données d'un utilisateur (Email, Mot de passe et Téléphone)
     * Centralisé ici pour être utilisé par UserController et AuthController
     */
    public static function validateData(array $data, bool $passwordRequired = true): array
    {
        $errors = [];

        if (empty($data['nom']))    $errors[] = 'Le nom est obligatoire.';
        if (empty($data['prenom'])) $errors[] = 'Le prénom est obligatoire.';

        // Validation Email
        if (empty($data['email'])) {
            $errors[] = "L'adresse email est obligatoire.";
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Le format de l'adresse email est invalide.";
        }

        // Validation Téléphone
        // On force le rôle en minuscule pour comparer sans erreur de casse
        $role = strtolower($data['role'] ?? '');
        $telephone = $data['telephone'] ?? '';

        if ($role === 'etudiant' || $role === 'pilote' || $role === 'entreprise') {
            if (empty($telephone)) {
                $errors[] = 'Le numéro de téléphone est obligatoire.';
            } else {
                // Regex : exact 10 chiffres (on nettoie les espaces/points éventuels pour le test)
                $cleanTel = str_replace([' ', '.', '-', '/'], '', $telephone);
                if (!preg_match('/^[0-9]{10}$/', $cleanTel)) {
                    $errors[] = 'Le format du numéro de téléphone est invalide (10 chiffres attendus).';
                }
            }
        }

        // Validation Mot de passe (Regex : 8 carac, 1 Maj, 1 Chiffre)
        $pwd = $data['password'] ?? '';
        if ($passwordRequired || !empty($pwd)) {
            $regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/';
            if (!preg_match($regex, $pwd)) {
                $errors[] = 'Le mot de passe doit contenir au moins 8 caractères, une majuscule et un chiffre.';
            }
        }

        return $errors;
    }

    public function update(int $id, array $data): bool
    {
        if (empty($data)) return false;
        $fields = [];
        $params = [];
        foreach ($data as $key => $value) {
            // On ignore le password en clair
            if ($key === 'password') continue;
            
            // On autorise explicitement 'mot_de_passe' (le hash) ou tout autre champ
            $fields[] = "$key = ?";
            $params[] = $value;
        }
        if (empty($fields)) return false;
        $params[] = $id;
        $sql = "UPDATE user SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function getStudentsByPilote(int $piloteId): array
    {
        $stmt = $this->db->prepare("SELECT id, nom, prenom, email, role, telephone FROM user WHERE role = 'etudiant' AND pilote_id = ? ORDER BY nom ASC");
        $stmt->execute([$piloteId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPilotes(): array
    {
        $stmt = $this->db->prepare("SELECT id, nom, prenom, telephone FROM user WHERE role = 'pilote' ORDER BY nom ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM user WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("INSERT INTO user (nom, prenom, email, telephone, mot_de_passe, role, pilote_id, company_id) VALUES (:nom, :prenom, :email, :telephone, :mot_de_passe, :role, :pilote_id, :company_id)");
        return $stmt->execute([
            'nom'          => $data['nom'],
            'prenom'       => $data['prenom'],
            'email'        => $data['email'],
            'telephone'    => $data['telephone'] ?? null,
            'mot_de_passe' => password_hash($data['password'], PASSWORD_BCRYPT),
            'role'         => $data['role'] ?? 'visiteur',
            'pilote_id'    => $data['pilote_id'] ?? null,
            'company_id'   => $data['company_id'] ?? null,
        ]) ? (int)$this->db->lastInsertId() : 0;
    }

    public function updateCompanyLink(int $userId, int $companyId, string $role = 'entreprise'): bool
    {
        $stmt = $this->db->prepare("UPDATE user SET company_id = ?, role = ? WHERE id = ?");
        return $stmt->execute([$companyId, $role, $userId]);
    }

    public function updateRole(int $userId, string $newRole): bool
    {
        $stmt = $this->db->prepare("UPDATE user SET role = ? WHERE id = ?");
        return $stmt->execute([$newRole, $userId]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM user WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function search(int $page, int $perPage, string $q = '', string $role = '', ?int $piloteId = null): array
    {
        $where = [];
        $params = [];

        $where[] = "role NOT IN ('entreprise', 'visiteur')";

        if ($q !== '') {
            $where[] = "(nom LIKE ? OR prenom LIKE ? OR email LIKE ? OR telephone LIKE ?)";
            $params[] = "%{$q}%";
            $params[] = "%{$q}%";
            $params[] = "%{$q}%";
            $params[] = "%{$q}%";
        }

        if ($role !== '') {
            $where[] = "role = ?";
            $params[] = $role;
        }

        if ($piloteId !== null) {
            $where[] = "pilote_id = ?";
            $params[] = $piloteId;
        }

        $whereClause = implode(' AND ', $where);
        return $this->paginate($page, $perPage, $whereClause, $params);
    }

    public function countStudentsByPilote(int $piloteId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM user WHERE role = 'etudiant' AND pilote_id = ?");
        $stmt->execute([$piloteId]);
        return (int)$stmt->fetchColumn();
    }

    public function countStudentsSearchingByPilote(int $piloteId): int
    {
        $sql = "SELECT COUNT(*) FROM user u 
                WHERE u.role = 'etudiant' AND u.pilote_id = ? 
                AND NOT EXISTS (SELECT 1 FROM application a WHERE a.etudiant_id = u.id AND a.statut = 'acceptee')";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$piloteId]);
        return (int)$stmt->fetchColumn();
    }
}
