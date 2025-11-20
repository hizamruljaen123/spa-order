<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Package_model extends CI_Model
{
    protected $table = 'package';

    public function get_all($include_deleted = false)
    {
        // Urutkan berdasarkan hands dulu, baru name
        $this->db->order_by('hands', 'ASC');
        $this->db->order_by('name', 'ASC');

        if (!$include_deleted) {
            $this->db->where('is_deleted', 0);
        }

        return $this->db->get($this->table)->result();
    }


    public function get_by_id($id, $include_deleted = false)
    {
        if (!$include_deleted) {
            return $this->db->get_where($this->table, ['id' => (int)$id, 'is_deleted' => 0])->row();
        }
        return $this->db->get_where($this->table, ['id' => (int)$id])->row();
    }

    public function create(array $data)
    {
        // Ensure new packages are not marked as deleted
        $data['is_deleted'] = $data['is_deleted'] ?? 0;
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, array $data)
    {
        // Don't allow updating is_deleted through normal update
        unset($data['is_deleted']);
        return $this->db->where('id', (int)$id)->update($this->table, $data);
    }

    /**
     * Soft delete - mark package as deleted instead of removing it
     * This prevents foreign key constraint errors while preserving historical data
     */
    public function delete($id)
    {
        return $this->db->where('id', (int)$id)->update($this->table, ['is_deleted' => 1]);
    }

    /**
     * Hard delete - completely remove package (use with caution)
     * Only use this if you're sure there are no foreign key dependencies
     */
    public function hard_delete($id)
    {
        return $this->db->where('id', (int)$id)->delete($this->table);
    }

    /**
     * Restore a soft-deleted package
     */
    public function restore($id)
    {
        return $this->db->where('id', (int)$id)->update($this->table, ['is_deleted' => 0]);
    }

    /**
     * Get soft-deleted packages (for admin view)
     */
    public function get_deleted_packages()
    {
        return $this->db->where('is_deleted', 1)->order_by('name', 'ASC')->get($this->table)->result();
    }

    /**
     * Get all packages including deleted ones (for admin purposes)
     */
    public function get_all_with_deleted()
    {
        $this->db->order_by('hands', 'ASC');
        $this->db->order_by('name', 'ASC');

        return $this->get_all(true); // true = include deleted data
    }


    /**
     * Check if a package is soft-deleted
     */
    public function is_deleted($id)
    {
        $package = $this->db->select('is_deleted')->where('id', (int)$id)->get($this->table)->row();
        return $package ? (bool)$package->is_deleted : false;
    }

    public function get_popular_packages($limit = 5)
    {
        $this->db->select('p.*, COUNT(b.id) AS total_booking')
                 ->from($this->table . ' p')
                 ->join('booking b', 'b.package_id = p.id', 'left')
                 ->where('p.is_deleted', 0) // Only count non-deleted packages
                 ->group_by('p.id')
                 ->order_by('total_booking', 'DESC')
                 ->limit((int)$limit);
        return $this->db->get()->result();
    }

    /**
     * Get packages for booking form - only active (non-deleted) packages
     */
    public function get_active_packages()
    {
        return $this->get_all(false);
    }
}