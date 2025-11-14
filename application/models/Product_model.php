<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Product Model
 *
 * @property CI_DB_mysqli_driver $db
 */
class Product_model extends CI_Model {

    public $table = 'products';

    public function __construct() {
        parent::__construct();
    }

    // Get all active products ordered by display_order
    public function get_active_products() {
        $this->db->where('is_active', 1);
        $this->db->order_by('display_order', 'ASC');
        $query = $this->db->get($this->table);
        return $query->result_array();
    }

    // Get all products
    public function get_all_products() {
        $this->db->order_by('display_order', 'ASC');
        $query = $this->db->get($this->table);
        return $query->result_array();
    }

    // Get related products (excluding current product)
    public function get_related_products($current_product_id, $limit = 4) {
        $this->db->where('id !=', $current_product_id);
        $this->db->where('is_active', 1);
        $this->db->order_by('display_order', 'ASC');
        $this->db->limit($limit);
        $query = $this->db->get($this->table);
        return $query->result_array();
    }

    // Get a single product by its ID
    public function get_product_by_id($id) {
        $this->db->where('id', $id);
        $query = $this->db->get($this->table);
        return $query->row_array();
    }

    // Insert a new product
    public function insert_product($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    // Update an existing product
    public function update_product($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        $this->db->update($this->table, $data);
        return $this->db->affected_rows();
    }

    // Delete a product
    public function delete_product($id) {
        $this->db->where('id', $id);
        $this->db->delete($this->table);
        return $this->db->affected_rows();
    }
}
?>