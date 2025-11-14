<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings_model extends CI_Model
{
    protected $table = 'app_settings';

    /**
     * Ensure settings table exists and bootstrap required keys.
     */
    public function ensure_bootstrap()
    {
        // Avoid attempting CREATE TABLE on every request,
        // which can fail on production if the DB user lacks CREATE privilege.
        if (!$this->db->table_exists($this->table)) {
            $sql = "CREATE TABLE IF NOT EXISTS `app_settings` (
              `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              `key` VARCHAR(100) NOT NULL UNIQUE,
              `value` TEXT NULL,
              `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
            $this->db->query($sql);
        }

        // Only proceed to ensure keys if the table is present
        if ($this->db->table_exists($this->table)) {
            $this->ensure_key('telegram_bot_token', '');
            $this->ensure_key('telegram_chat_id', '');
            $this->ensure_key('whatsapp_phone', '+60143218026');
        }
    }

    private function ensure_key($key, $default = '')
    {
        $row = $this->db->get_where($this->table, ['key' => $key], 1)->row();
        if (!$row) {
            $this->db->insert($this->table, [
                'key'        => $key,
                'value'      => $default,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    /**
     * Get a single setting by key.
     */
    public function get($key, $default = null)
    {
        $row = $this->db->get_where($this->table, ['key' => $key], 1)->row();
        if ($row && isset($row->value)) {
            return (string)$row->value;
        }
        return $default;
    }

    /**
     * Upsert a single setting key/value.
     */
    public function set($key, $value)
    {
        $sql = "INSERT INTO `{$this->table}` (`key`,`value`,`updated_at`) VALUES (?, ?, NOW())
                ON DUPLICATE KEY UPDATE `value`=VALUES(`value`), `updated_at`=NOW()";
        return $this->db->query($sql, [$key, (string)$value]);
    }

    /**
     * Get multiple keys at once.
     * Returns associative array key => value (null if missing).
     */
    public function get_many(array $keys)
    {
        if (empty($keys)) return [];
        $this->db->select('`key`,`value`')->from($this->table)->where_in('key', $keys);
        $rows = $this->db->get()->result();
        $out = [];
        foreach ($rows as $r) {
            $out[$r->key] = $r->value;
        }
        foreach ($keys as $k) {
            if (!array_key_exists($k, $out)) {
                $out[$k] = null;
            }
        }
        return $out;
    }

    /**
     * Upsert many keys at once.
     */
    public function set_many(array $assoc)
    {
        $ok = true;
        foreach ($assoc as $k => $v) {
            $res = $this->set($k, $v);
            $ok = $ok && (bool)$res;
        }
        return $ok;
    }

    /**
     * Return all settings as associative array.
     */
    public function all()
    {
        $rows = $this->db->select('`key`,`value`')->from($this->table)->get()->result();
        $out = [];
        foreach ($rows as $r) {
            $out[$r->key] = $r->value;
        }
        return $out;
    }
}