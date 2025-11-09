<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_model extends CI_Model
{
    /**
     * Monthly stats for a given year.
     * Returns array indexed by month (1..12) with:
     * - total_pendapatan (float)
     * - total_booking (int)
     */
    public function get_monthly_stats($year = null)
    {
        $year = $year ?: date('Y');

        $this->db->select('MONTH(date) AS month, SUM(total_price) AS total_pendapatan, COUNT(id) AS total_booking', false)
                 ->from('booking')
                 ->where('YEAR(date)', (int)$year)
                 ->group_by('MONTH(date)')
                 ->order_by('MONTH(date)', 'ASC');

        $rows = $this->db->get()->result();

        // Initialize all months with zero values
        $stats = [];
        for ($m = 1; $m <= 12; $m++) {
            $stats[$m] = [
                'total_pendapatan' => 0.0,
                'total_booking'    => 0,
            ];
        }

        foreach ($rows as $r) {
            $month = (int)$r->month;
            $stats[$month] = [
                'total_pendapatan' => (float)$r->total_pendapatan,
                'total_booking'    => (int)$r->total_booking,
            ];
        }

        return $stats;
    }

    /**
     * Summary metrics for a given day (defaults to today).
     * - total_booking_today
     * - total_pendapatan_today
     */
    public function get_today_summary($date = null)
    {
        $date = $date ?: date('Y-m-d');

        $this->db->select('COUNT(id) AS total_booking, COALESCE(SUM(total_price),0) AS total_pendapatan', false)
                 ->from('booking')
                 ->where('date', $date);

        $row = $this->db->get()->row();

        return [
            'total_booking_today'     => $row ? (int)$row->total_booking : 0,
            'total_pendapatan_today'  => $row ? (float)$row->total_pendapatan : 0.0,
        ];
    }

    /**
     * Popular packages by number of bookings within a date range (optional).
     */
    public function get_popular_packages($from = null, $to = null, $limit = 5)
    {
        $this->db->select('p.id, p.name, COUNT(b.id) AS total_booking, COALESCE(SUM(b.total_price),0) AS total_pendapatan', false)
                 ->from('package p')
                 ->join('booking b', 'b.package_id = p.id', 'left');

        if ($from) {
            $this->db->where('b.date >=', $from);
        }
        if ($to) {
            $this->db->where('b.date <=', $to);
        }

        $this->db->group_by('p.id')
                 ->order_by('total_booking', 'DESC')
                 ->limit((int)$limit);

        return $this->db->get()->result();
    }

    /**
     * Revenue and booking count between dates (inclusive).
     */
    public function get_revenue_range($from, $to)
    {
        $this->db->select('COUNT(id) AS total_booking, COALESCE(SUM(total_price),0) AS total_pendapatan', false)
                 ->from('booking')
                 ->where('date >=', $from)
                 ->where('date <=', $to);

        $row = $this->db->get()->row();

        return [
            'total_booking'   => $row ? (int)$row->total_booking : 0,
            'total_pendapatan'=> $row ? (float)$row->total_pendapatan : 0.0,
        ];
    }

    /**
     * Dashboard summary for quick metrics.
     * Includes: total_booking_today, total_pendapatan_today, active_therapists, popular_packages
     */
    public function get_dashboard_summary()
    {
        $today = $this->get_today_summary();

        // Active therapists count
        $this->db->from('therapist')
                 ->where('status', 'available');
        $activeTherapists = $this->db->count_all_results();

        // Popular packages (last 30 days)
        $to = date('Y-m-d');
        $from = date('Y-m-d', strtotime('-30 days'));
        $popular = $this->get_popular_packages($from, $to, 5);

        return [
            'total_booking_today'    => $today['total_booking_today'],
            'total_pendapatan_today' => $today['total_pendapatan_today'],
            'active_therapists'      => (int)$activeTherapists,
            'popular_packages'       => $popular,
        ];
    }
}