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
    /**
     * Hourly distribution of bookings for a given year.
     * Returns array of 24 integers indexed by hour 0..23.
     */
    public function get_hourly_distribution($year = null)
    {
        $year = $year ?: date('Y');

        $this->db->select('HOUR(time) AS hour, COUNT(id) AS total', false)
                 ->from('booking')
                 ->where('YEAR(date)', (int)$year)
                 ->group_by('HOUR(time)')
                 ->order_by('HOUR(time)', 'ASC');

        $rows = $this->db->get()->result();

        $hours = array_fill(0, 24, 0);
        foreach ($rows as $r) {
            $h = (int)$r->hour;
            if ($h >= 0 && $h <= 23) {
                $hours[$h] = (int)$r->total;
            }
        }
        return $hours;
    }

    /**
     * Weekday distribution of bookings for a given year.
     * Returns array of 7 integers ordered Monday..Sunday.
     */
    public function get_weekday_distribution($year = null)
    {
        $year = $year ?: date('Y');

        // MySQL DAYOFWEEK(): 1=Sunday, 2=Monday, ... 7=Saturday
        $this->db->select('DAYOFWEEK(date) AS dow, COUNT(id) AS total', false)
                 ->from('booking')
                 ->where('YEAR(date)', (int)$year)
                 ->group_by('DAYOFWEEK(date)')
                 ->order_by('DAYOFWEEK(date)', 'ASC');

        $rows = $this->db->get()->result();

        $byDow = [];
        for ($i = 1; $i <= 7; $i++) $byDow[$i] = 0;
        foreach ($rows as $r) {
            $d = (int)$r->dow;
            if ($d >= 1 && $d <= 7) {
                $byDow[$d] = (int)$r->total;
            }
        }

        // Reorder to Monday..Sunday (2..7,1)
        return [
            $byDow[2], // Monday
            $byDow[3], // Tuesday
            $byDow[4], // Wednesday
            $byDow[5], // Thursday
            $byDow[6], // Friday
            $byDow[7], // Saturday
            $byDow[1], // Sunday
        ];
    }

    /**
     * Day-hour heatmap for a given year.
     * Returns 2D array [7][24] ordered Monday..Sunday, hour 0..23.
     */
    public function get_day_hour_heatmap($year = null)
    {
        $year = $year ?: date('Y');

        $this->db->select('DAYOFWEEK(date) AS dow, HOUR(time) AS hour, COUNT(id) AS total', false)
                 ->from('booking')
                 ->where('YEAR(date)', (int)$year)
                 ->group_by(['DAYOFWEEK(date)', 'HOUR(time)'])
                 ->order_by('DAYOFWEEK(date)', 'ASC')
                 ->order_by('HOUR(time)', 'ASC');

        $rows = $this->db->get()->result();

        // Initialize [7][24] zero matrix for Monday..Sunday
        $matrix = [];
        for ($d = 0; $d < 7; $d++) {
            $matrix[$d] = array_fill(0, 24, 0);
        }

        foreach ($rows as $r) {
            $dow = (int)$r->dow;   // 1..7
            $hour = (int)$r->hour; // 0..23
            $val = (int)$r->total;

            if ($hour < 0 || $hour > 23) continue;
            // Map MySQL dow (1=Sun) to index (Mon..Sun => 0..6)
            // Order: Mon(2)->0, Tue(3)->1, ..., Sat(7)->5, Sun(1)->6
            $map = [2 => 0, 3 => 1, 4 => 2, 5 => 3, 6 => 4, 7 => 5, 1 => 6];
            if (isset($map[$dow])) {
                $matrix[$map[$dow]][$hour] = $val;
            }
        }

        return $matrix;
    }

    /**
     * Top therapists by number of bookings for a given year.
     * Returns array of objects: id, name, total_booking.
     */
    public function get_top_therapists($year = null, $limit = 5)
    {
        $year = $year ?: date('Y');

        $this->db->select('t.id, t.name, COUNT(b.id) AS total_booking', false)
                 ->from('therapist t')
                 ->join('booking b', 'b.therapist_id = t.id', 'inner')
                 ->where('YEAR(b.date)', (int)$year)
                 ->group_by(['t.id', 't.name'])
                 ->order_by('total_booking', 'DESC')
                 ->limit((int)$limit);

        return $this->db->get()->result();
    }

    /**
     * Top packages by number of bookings for a given year.
     * Returns array of objects: id, name, total_booking.
     */
    public function get_top_packages_by_count($year = null, $limit = 5)
    {
        $year = $year ?: date('Y');

        $this->db->select('p.id, p.name, COUNT(b.id) AS total_booking', false)
                 ->from('package p')
                 ->join('booking b', 'b.package_id = p.id', 'inner')
                 ->where('YEAR(b.date)', (int)$year)
                 ->group_by(['p.id', 'p.name'])
                 ->order_by('total_booking', 'DESC')
                 ->limit((int)$limit);

        return $this->db->get()->result();
    }
}