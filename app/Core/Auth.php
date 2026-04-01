<?php

namespace App\Core;

use App\Models\User;

class Auth
{
    /** Démarre la session si pas encore démarrée */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            $cfg = require dirname(__DIR__, 2) . '/config/app.php';

            session_name($cfg['session_name'] ?? 'STAGELAB_SESS');
            session_set_cookie_params([
                'lifetime' => 0,
                'path'     => '/',
                'secure'   => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Strict',
            ]);
            session_start();
        }
    }

    public static function set(array $user): void
    {
        self::login($user);
    }

    /** Connecte un utilisateur (Ajout du company_id ici !) */
    public static function login(array $user): void
    {
        self::start();
        
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id'         => $user['id'],
            'nom'        => $user['nom'],
            'prenom'     => $user['prenom'],
            'email'      => $user['email'],
            'role'       => $user['role'],
            'company_id' => $user['company_id'] ?? null, // CRUCIAL : sans ça, le dashboard reste vide
        ];
    }

    /** * MAJ de la session sans reconnexion
     * Utile quand un admin approuve le compte
     */
    public static function refresh(): void
    {
        self::start();
        $id = self::id();
        if ($id) {
            $userModel = new User();
            $user = $userModel->find($id);
            if ($user) {
                // On réécrase la session avec les données fraîches de la DB
                self::login($user);
            }
        }
    }

    public static function logout(): void
    {
        self::start();
        $_SESSION = [];
        session_destroy();
    }

    public static function check(): bool
    {
        self::start();
        return isset($_SESSION['user']);
    }

    public static function user(): ?array
    {
        self::start();
        return $_SESSION['user'] ?? null;
    }

    public static function role(): ?string
    {
        self::start();
        return $_SESSION['user']['role'] ?? null;
    }

    public static function id(): ?int
    {
        self::start();
        return isset($_SESSION['user']['id']) ? (int)$_SESSION['user']['id'] : null;
    }

    public static function hasRole(string ...$roles): bool
    {
        return in_array(self::role(), $roles, true);
    }

    public static function csrfToken(): string
    {
        self::start();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function verifyCsrf(): bool
    {
        self::start();
        $token = $_POST['_csrf'] ?? '';
        return hash_equals($_SESSION['csrf_token'] ?? '', $token);
    }
}
