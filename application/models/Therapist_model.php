<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Therapist_model extends CI_Model
{
    protected $table = 'therapist';

    public function get_all($only_active = false)
    {
        if ($only_active) {
            $this->db->where('status', 'available');
        }
        return $this->db->order_by('name', 'ASC')->get($this->table)->result();
    }

    public function get_by_id($id)
    {
        return $this->db->get_where($this->table, ['id' => (int)$id])->row();
    }

    public function create(array $data)
    {
        $data = $this->filter_fillable($data);
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, array $data)
    {
        $data = $this->filter_fillable($data);
        return $this->db->where('id', (int)$id)->update($this->table, $data);
    }

    public function delete($id)
    {
        return $this->db->where('id', (int)$id)->delete($this->table);
    }

    public function set_status($id, $status)
    {
        $allowed = ['available', 'busy', 'off'];
        if (!in_array($status, $allowed, true)) {
            return false;
        }
        return $this->db->where('id', (int)$id)->update($this->table, ['status' => $status]);
    }

    /**
     * Get available therapists on a specific date and time:
     * - Therapist status must be 'available'
     * - Not already booked at the given date+time with pending/confirmed
     */
    public function get_available_on($date, $time)
    {
        $this->db->select('t.*')
                 ->from($this->table . ' t')
                 ->join(
                    'booking b',
                    "b.therapist_id = t.id AND b.date = " . $this->db->escape($date) . " AND b.time = " . $this->db->escape($time) . " AND b.status IN ('pending','confirmed')",
                    'left',
                    false
                 )
                 ->where('t.status', 'available')
                 ->where('b.id IS NULL', null, false)
                 ->order_by('t.name', 'ASC');

        return $this->db->get()->result();
    }

    private function filter_fillable(array $data)
    {
        $fillable = ['name', 'phone', 'status', 'created_at'];
        return array_intersect_key($data, array_flip($fillable));
    }
}