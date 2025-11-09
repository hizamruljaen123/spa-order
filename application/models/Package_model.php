<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Package_model extends CI_Model
{
    protected $table = 'package';

    public function get_all()
    {
        return $this->db->order_by('name', 'ASC')->get($this->table)->result();
    }

    public function get_by_id($id)
    {
        return $this->db->get_where($this->table, ['id' => (int)$id])->row();
    }

    public function create(array $data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, array $data)
    {
        return $this->db->where('id', (int)$id)->update($this->table, $data);
    }

    public function delete($id)
    {
        return $this->db->where('id', (int)$id)->delete($this->table);
    }

    public function get_popular_packages($limit = 5)
    {
        $this->db->select('p.*, COUNT(b.id) AS total_booking')
                 ->from($this->table . ' p')
                 ->join('booking b', 'b.package_id = p.id', 'left')
                 ->group_by('p.id')
                 ->order_by('total_booking', 'DESC')
                 ->limit((int)$limit);
        return $this->db->get()->result();
    }
}