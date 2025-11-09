<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice_model extends CI_Model
{
    protected $table = 'invoice';

    public function get_by_id($id)
    {
        return $this->db->get_where($this->table, ['id' => (int)$id])->row();
    }

    public function get_by_booking($booking_id)
    {
        return $this->db->get_where($this->table, ['booking_id' => (int)$booking_id])->row();
    }

    public function update_payment_status($invoice_id, $status)
    {
        $allowed = ['DP', 'Lunas'];
        if (!in_array($status, $allowed, true)) {
            return false;
        }
        return $this->db->where('id', (int)$invoice_id)->update($this->table, ['payment_status' => $status]);
    }

    public function delete($id)
    {
        return $this->db->where('id', (int)$id)->delete($this->table);
    }

    /**
     * Generate invoice number with pattern: INV-YYYYMMDD-#### (sequence per day)
     */
    public function generate_invoice_number()
    {
        $prefix = 'INV-' . date('Ymd') . '-';

        // Count existing invoices with today's prefix to generate next sequence
        $this->db->like('invoice_number', $prefix, 'after');
        $count = $this->db->count_all_results($this->table);

        $seq = str_pad($count + 1, 4, '0', STR_PAD_LEFT);
        return $prefix . $seq;
    }

    /**
     * Create invoice for a booking. If already exists, return existing id (idempotent).
     */
    public function create_for_booking($booking_id, $payment_status = 'DP')
    {
        $booking_id = (int)$booking_id;

        // If invoice already exists, return it
        $existing = $this->get_by_booking($booking_id);
        if ($existing) {
            return (int)$existing->id;
        }

        // Fetch booking to get total_price
        $booking = $this->db->select('id,total_price')->from('booking')->where('id', $booking_id)->get()->row();
        if (!$booking) {
            return false;
        }

        $invoice_number = $this->generate_invoice_number();
        $status = in_array($payment_status, ['DP', 'Lunas'], true) ? $payment_status : 'DP';

        $data = [
            'booking_id'     => $booking_id,
            'invoice_number' => $invoice_number,
            'total'          => (float)$booking->total_price,
            'payment_status' => $status,
            'created_at'     => date('Y-m-d H:i:s'),
        ];

        $this->db->insert($this->table, $data);
        return (int)$this->db->insert_id();
    }
}