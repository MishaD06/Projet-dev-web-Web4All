<?php

namespace App\Models;

use App\Core\Model;

class Skill extends Model
{
    protected string $table = 'skill';

    public function all(): array
    {
        return $this->db->query("SELECT * FROM skill ORDER BY nom")->fetchAll();
    }
}
