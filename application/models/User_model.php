<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property CI_DB_query_builder $db
 */
class User_model extends CI_Model
{
    private $table = 'admin_user';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Ensure admin_user table exists and seed a default admin account
     * Default credentials:
     *   username: admin
     *   password: admin123
     */
    public function ensure_bootstrap()
    {
        // Create table if not exists
        $sql = "CREATE TABLE IF NOT EXISTS `admin_user` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `username` VARCHAR(50) NOT NULL UNIQUE,
            `password_hash` VARCHAR(255) NOT NULL,
            `name` VARCHAR(100) NULL,
            `role` VARCHAR(20) NOT NULL DEFAULT 'admin',
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
        $this->db->query($sql);

        // If no users, create default admin
        $count = (int)$this->db->count_all($this->table);
        if ($count === 0) {
            $password = 'admin123';
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $this->db->insert($this->table, [
                'username' => 'admin',
                'password_hash' => $hash,
                'name' => 'Administrator',
                'role' => 'admin',
            ]);
        }
    }

    public function get_by_username($username)
    {
        return $this->db->get_where($this->table, ['username' => $username], 1)->row();
    }

    /**
     * Verify login credentials
     * @return object|false User row on success, false on failure
     */
    public function verify_login($username, $password)
    {
        $user = $this->get_by_username($username);
        if (!$user) return false;
        if (!isset($user->password_hash)) return false;
        if (password_verify($password, $user->password_hash)) {
            return $user;
        }
        return false;
    }

    /**
     * Create a new admin user
     * Accepts 'password' (plain) and converts to 'password_hash'
     */
    public function create($data)
    {
        if (isset($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_BCRYPT);
            unset($data['password']);
        }
        $ok = $this->db->insert($this->table, $data);
        if (!$ok) {
            return false;
        }
        if (isset($data['username'])) {
            $row = $this->get_by_username($data['username']);
            return $row ? (int)$row->id : true;
        }
        return true;
    }
}