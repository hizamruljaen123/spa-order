<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Exclusive Treatment Model
 *
 * @property CI_DB_mysqli_driver $db
 */
class Exclusive_treatment_model extends CI_Model {

    public $table = 'exclusive_treatments';

    public function __construct() {
        parent::__construct();
    }

    // Get all active exclusive treatments ordered by display_order
    public function get_active_treatments()
    {
        $this->db->where('is_active', 1);
        // $this->db->order_by('display_order', 'ASC');
        $this->db->order_by('name', 'ASC'); 

        return $this->db->get($this->table)->result_array();
    }


    // Get treatments by category
    public function get_treatments_by_category($category = null) {
        $this->db->where('is_active', 1);
        if ($category) {
            $this->db->where('category', $category);
        }
        $this->db->order_by('display_order', 'ASC');
        $query = $this->db->get($this->table);
        return $query->result_array();
    }

    // Get all treatments (active and inactive)
    public function get_all_treatments() {
        $this->db->order_by('display_order', 'ASC');
        $query = $this->db->get($this->table);
        return $query->result_array();
    }

    // Get a single treatment by its ID
    public function get_treatment_by_id($id) {
        $this->db->where('id', $id);
        $query = $this->db->get($this->table);
        return $query->row_array();
    }

    // Insert a new treatment
    public function insert_treatment($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    // Update an existing treatment
    public function update_treatment($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        $this->db->update($this->table, $data);
        return $this->db->affected_rows();
    }

    // Delete a treatment
    public function delete_treatment($id) {
        $this->db->where('id', $id);
        $this->db->delete($this->table);
        return $this->db->affected_rows();
    }

    // Get treatments grouped by category
    public function get_treatments_grouped() {
        $treatments = $this->get_active_treatments();
        $grouped = [];

        foreach ($treatments as $treatment) {
            $category = $treatment['category'];
            if (!isset($grouped[$category])) {
                $grouped[$category] = [];
            }
            $grouped[$category][] = $treatment;
        }

        return $grouped;
    }

    // Get main treatments (is_add_on = 0) as packages
    public function get_main_treatments_as_packages() {
        $this->db->where('is_active', 1);
        $this->db->where('is_add_on', 0);
        $this->db->order_by('display_order', 'ASC');
        $query = $this->db->get($this->table);
        $treatments = $query->result_array();

        // Format as package objects for compatibility
        $packages = [];
        foreach ($treatments as $treatment) {
            $packages[] = (object) [
                'id' => 'treatment_' . $treatment['id'], // Prefix to distinguish
                'name' => $treatment['name'],
                'category' => $treatment['category'],
                'duration' => null, // exclusive treatments don't have duration
                'price_in_call' => (float)$treatment['price'],
                'price_out_call' => (float)$treatment['price'], // same price for both
                'currency' => $treatment['currency'],
                'description' => $treatment['description'],
                'type' => 'exclusive_treatment',
                'original_id' => $treatment['id']
            ];
        }
        return $packages;
    }

    // Get add-on treatments (is_add_on = 1 or category = 'addon')
    public function get_addon_treatments() {
        $this->db->where('is_active', 1);
        $this->db->group_start();
        $this->db->where('is_add_on', 1);
        $this->db->or_where('category', 'addon');
        $this->db->group_end();
        $this->db->order_by('display_order', 'ASC');
        $query = $this->db->get($this->table);
        $treatments = $query->result_array();

        // Format as addon objects for compatibility
        $addons = [];
        foreach ($treatments as $treatment) {
            $addons[] = (object) [
                'id' => 'treatment_' . $treatment['id'], // Prefix to distinguish
                'category' => $treatment['category'],
                'name' => $treatment['name'],
                'description' => $treatment['description'],
                'price' => (float)$treatment['price'],
                'currency' => $treatment['currency'],
                'type' => 'exclusive_addon',
                'original_id' => $treatment['id']
            ];
        }
        return $addons;
    }

    // Get treatment by combined ID (removes prefix and gets original)
    public function get_treatment_by_combined_id($combined_id) {
        if (strpos($combined_id, 'treatment_') === 0) {
            $id = str_replace('treatment_', '', $combined_id);
            return $this->get_treatment_by_id($id);
        }
        return null;
    }
}
?>