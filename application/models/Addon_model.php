<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Addon_model extends CI_Model
{
    protected $table = 'add_on';
    protected $pivot = 'booking_addon';

    public function get_all($only_active = false)
    {
        if ($only_active) {
            $this->db->where('is_active', 1);
        }
        return $this->db->order_by('category', 'ASC')->order_by('name', 'ASC')->get($this->table)->result();
    }

    public function get_active_grouped()
    {
        $rows = $this->get_all(true);
        $grouped = [];
        foreach ($rows as $r) {
            $cat = isset($r->category) && $r->category !== '' ? $r->category : 'Others';
            if (!isset($grouped[$cat])) {
                $grouped[$cat] = [];
            }
            $grouped[$cat][] = $r;
        }
        return $grouped;
    }

    public function get_by_id($id)
    {
        return $this->db->get_where($this->table, ['id' => (int)$id])->row();
    }

    /**
     * Returns map of id => row for given ids
     */
    public function get_by_ids(array $ids)
    {
        $ids = array_values(array_unique(array_map('intval', $ids)));
        if (empty($ids)) {
            return [];
        }
        $rows = $this->db->where_in('id', $ids)->get($this->table)->result();
        $map = [];
        foreach ($rows as $r) {
            $map[(int)$r->id] = $r;
        }
        return $map;
    }

    public function create(array $data)
    {
        $data = $this->filter_fillable($data);
        if (!isset($data['is_active'])) {
            $data['is_active'] = 1;
        }
        if (!isset($data['currency']) || $data['currency'] === '') {
            $data['currency'] = 'RM';
        }
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

    protected function filter_fillable(array $data)
    {
        $fillable = ['category', 'name', 'description', 'price', 'currency', 'is_active'];
        return array_intersect_key($data, array_flip($fillable));
    }

    /**
     * Attach selected add-ons to a booking (defaults qty=1 if not provided)
     * @param int $booking_id
     * @param array $items array of ints [id,...] or array of ['id'=>int,'qty'=>int]
     * @return int number of inserted rows
     */
    public function attach_to_booking($booking_id, array $items)
    {
        if (empty($items)) {
            return 0;
        }
        $ids = [];
        $qtyMap = [];
        foreach ($items as $k => $v) {
            if (is_array($v)) {
                $id = isset($v['id']) ? (int)$v['id'] : (int)$k;
                $qty = isset($v['qty']) ? max(1, (int)$v['qty']) : 1;
            } else {
                $id = (int)$v;
                $qty = 1;
            }
            if ($id > 0) {
                $ids[] = $id;
                $qtyMap[$id] = $qty;
            }
        }
        $ids = array_values(array_unique($ids));
        if (empty($ids)) {
            return 0;
        }

        $map = $this->get_by_ids($ids);
        $count = 0;
        foreach ($ids as $id) {
            if (!isset($map[$id])) {
                continue;
            }
            $row = $map[$id];
            $data = [
                'booking_id' => (int)$booking_id,
                'add_on_id'  => (int)$id,
                'unit_price' => (float)$row->price,
                'qty'        => isset($qtyMap[$id]) ? (int)$qtyMap[$id] : 1,
            ];
            $this->db->insert($this->pivot, $data);
            $count += (int)$this->db->affected_rows();
        }
        return $count;
    }

    /**
     * Sum current prices for given add-on ids (ignores inactive flag intentionally)
     */
    public function get_total_for_ids(array $ids)
    {
        if (empty($ids)) {
            return 0.0;
        }
        $map = $this->get_by_ids($ids);
        $sum = 0.0;
        foreach ($ids as $id) {
            if (isset($map[$id])) {
                $sum += (float)$map[$id]->price;
            }
        }
        return (float)$sum;
    }

    /**
     * List pivot rows with add-on details for a booking
     */
    public function get_for_booking($booking_id)
    {
        $this->db->select('ba.*, ao.category, ao.name, ao.description, ao.currency')
            ->from($this->pivot . ' ba')
            ->join($this->table . ' ao', 'ao.id = ba.add_on_id', 'left')
            ->where('ba.booking_id', (int)$booking_id);
        return $this->db->get()->result();
    }
}