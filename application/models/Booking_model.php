<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Booking_model extends CI_Model
{
    protected $table = 'booking';

    /**
     * Create a new booking.
     * If total_price is not provided, it will be derived from the package price.
     */
    public function create(array $data)
    {
        $data = $this->filter_fillable($data);

        if (empty($data['status'])) {
            $data['status'] = 'pending';
        }

        if (empty($data['created_at'])) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }

        // Derive total_price from package based on call_type if not provided
        if (!isset($data['total_price']) || $data['total_price'] === null) {
            $callType = isset($data['call_type']) && in_array($data['call_type'], ['IN','OUT']) ? $data['call_type'] : 'IN';
            $data['total_price'] = $this->get_package_price((int)$data['package_id'], $callType);
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

    /**
     * Get one booking with joined package and therapist names.
     */
    public function get_by_id($id)
    {
        $this->db->select('b.*, p.name AS package_name, p.category AS package_category, p.hands AS package_hands, p.price_in_call AS price_in_call, p.price_out_call AS price_out_call, p.currency AS currency, t.name AS therapist_name, t.phone AS therapist_phone')
                 ->from($this->table . ' b')
                 ->join('package p', 'p.id = b.package_id', 'left')
                 ->join('therapist t', 't.id = b.therapist_id', 'left')
                 ->where('b.id', (int)$id);
        return $this->db->get()->row();
    }

    /**
     * Get bookings list with optional filters:
     * filters: from (Y-m-d), to (Y-m-d), therapist_id, package_id, status
     */
    public function get_all(array $filters = [], $limit = null, $offset = null)
    {
        $this->db->select('b.*, p.name AS package_name, t.name AS therapist_name')
                 ->from($this->table . ' b')
                 ->join('package p', 'p.id = b.package_id', 'left')
                 ->join('therapist t', 't.id = b.therapist_id', 'left')
                 ->order_by('b.date', 'DESC')
                 ->order_by('b.time', 'DESC');

        if (!empty($filters['from'])) {
            $this->db->where('b.date >=', $filters['from']);
        }
        if (!empty($filters['to'])) {
            $this->db->where('b.date <=', $filters['to']);
        }
        if (!empty($filters['therapist_id'])) {
            $this->db->where('b.therapist_id', (int)$filters['therapist_id']);
        }
        if (!empty($filters['package_id'])) {
            $this->db->where('b.package_id', (int)$filters['package_id']);
        }
        if (!empty($filters['status'])) {
            $this->db->where('b.status', $filters['status']);
        }

        if ($limit !== null) {
            $this->db->limit((int)$limit, (int)$offset);
        }

        return $this->db->get()->result();
    }

    /**
     * Mark a booking as confirmed.
     */
    public function mark_confirmed($id)
    {
        return $this->db->where('id', (int)$id)->update($this->table, ['status' => 'confirmed']);
    }

    /**
     * Mark a booking as completed.
     */
    public function mark_completed($id)
    {
        return $this->db->where('id', (int)$id)->update($this->table, ['status' => 'completed']);
    }

    /**
     * Cancel a booking.
     */
    public function cancel($id)
    {
        return $this->db->where('id', (int)$id)->update($this->table, ['status' => 'canceled']);
    }

    /**
     * Calendar events provider for FullCalendar endpoint.
     * Returns booked events between $start and $end (ISO 8601 or Y-m-d).
     * Note: Only emits booked events (pending/confirmed/completed). "Available" slots
     * are not explicitly stored; UI can infer availability by empty slots.
     */
    public function get_calendar_events($start, $end, $therapist_id = null)
    {
        $this->db->select('b.id, b.customer_name, b.date, b.time, b.status, p.name AS package_name, t.name AS therapist_name')
                 ->from($this->table . ' b')
                 ->join('package p', 'p.id = b.package_id', 'left')
                 ->join('therapist t', 't.id = b.therapist_id', 'left')
                 ->where('b.date >=', date('Y-m-d', strtotime($start)))
                 ->where('b.date <=', date('Y-m-d', strtotime($end)));

        if (!empty($therapist_id)) {
            $this->db->where('b.therapist_id', (int)$therapist_id);
        }

        $rows = $this->db->get()->result();

        $events = [];
        foreach ($rows as $r) {
            // Build ISO8601 datetime
            $startDt = $r->date . 'T' . $r->time;
            $title = sprintf('%s - %s (%s)', $r->customer_name, $r->package_name, $r->therapist_name ?: 'N/A');

            // Determine color: red for pending/confirmed, gray for completed, orange for canceled
            $color = '#28a745'; // default green (available) not used here
            if ($r->status === 'pending' || $r->status === 'confirmed') {
                $color = '#dc3545'; // red - booked
            } elseif ($r->status === 'completed') {
                $color = '#6c757d'; // gray
            } elseif ($r->status === 'canceled') {
                $color = '#fd7e14'; // orange
            }

            $events[] = [
                'id'    => (int)$r->id,
                'title' => $title,
                'start' => $startDt,
                'color' => $color,
                'extendedProps' => [
                    'status' => $r->status,
                    'customer_name' => $r->customer_name,
                    'package_name'  => $r->package_name,
                    'therapist_name'=> $r->therapist_name,
                ],
            ];
        }

        return $events;
    }

    /**
     * Helper to fetch package price by id.
     */
    private function get_package_price($package_id, $call_type = 'IN')
    {
        $row = $this->db
            ->select('price_in_call, price_out_call')
            ->from('package')
            ->where('id', (int)$package_id)
            ->get()
            ->row();

        if (!$row) {
            return 0.0;
        }

        if ($call_type === 'OUT') {
            return (float)$row->price_out_call;
        }
        return (float)$row->price_in_call;
    }

    private function filter_fillable(array $data)
    {
        $fillable = ['customer_name', 'address', 'therapist_id', 'package_id', 'call_type', 'date', 'time', 'total_price', 'status', 'created_at'];
        return array_intersect_key($data, array_flip($fillable));
    }

    /**
     * Get booked times (HH:MM) for a specific date (YYYY-MM-DD), optionally filtered by therapist.
     * Counts bookings with status pending/confirmed/completed as "booked". Canceled are ignored.
     *
     * @param string $date         Format YYYY-MM-DD
     * @param int|null $therapist_id Optional therapist ID filter
     * @return array                List of booked times in "HH:MM"
     */
    public function get_booked_times($date, $therapist_id = null)
    {
        if (empty($date)) {
            return [];
        }

        $this->db->select('time')
                 ->from($this->table)
                 ->where('date', $date)
                 ->where_in('status', ['pending', 'confirmed', 'completed']);

        if (!empty($therapist_id)) {
            $this->db->where('therapist_id', (int)$therapist_id);
        }

        $rows = $this->db->get()->result();
        $times = [];
        foreach ($rows as $r) {
            // Normalize to HH:MM
            $times[] = substr($r->time, 0, 5);
        }
        return array_values(array_unique($times));
    }
}