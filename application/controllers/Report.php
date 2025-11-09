<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Reports API/Pages
 *
 * @property CI_Input $input
 * @property CI_Output $output
 * @property Report_model $Report_model
 */
class Report extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->load->model('Report_model');
        $this->load->helper(['url']);
    }

    /**
     * Optional landing - redirect to admin report page.
     */
    public function index()
    {
        redirect('admin/report');
    }

    /**
     * GET /report/monthly?year=YYYY
     * Returns JSON payload:
     * {
     *   "year": 2025,
     *   "labels": ["Jan","Feb",...],
     *   "total_pendapatan": [0,12345,...],
     *   "total_booking": [0,12,...]
     * }
     */
    public function monthly()
    {
        $year = (int)$this->input->get('year', true);
        if (!$year) {
            $year = (int)date('Y');
        }

        $stats = $this->Report_model->get_monthly_stats($year);

        $labels = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $pendapatan = [];
        $booking = [];
        for ($m = 1; $m <= 12; $m++) {
            $pendapatan[] = (float)($stats[$m]['total_pendapatan'] ?? 0.0);
            $booking[]    = (int)($stats[$m]['total_booking'] ?? 0);
        }

        $payload = [
            'year' => $year,
            'labels' => $labels,
            'total_pendapatan' => $pendapatan,
            'total_booking' => $booking,
        ];

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($payload));
    }
    /**
     * GET /report/hourly?year=YYYY
     * Returns hourly distribution:
     * {
     *   "year": 2025,
     *   "labels": ["00","01",...,"23"],
     *   "total_booking": [..24 items..]
     * }
     */
    public function hourly()
    {
        $year = (int)$this->input->get('year', true);
        if (!$year) {
            $year = (int)date('Y');
        }

        $this->load->model('Report_model');
        $hours = $this->Report_model->get_hourly_distribution($year);

        $labels = [];
        for ($h = 0; $h < 24; $h++) {
            $labels[] = str_pad((string)$h, 2, '0', STR_PAD_LEFT);
        }

        $payload = [
            'year' => $year,
            'labels' => $labels,
            'total_booking' => array_values($hours),
        ];

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($payload));
    }

    /**
     * GET /report/weekday?year=YYYY
     * Returns weekday distribution Monday..Sunday:
     * {
     *   "year": 2025,
     *   "labels": ["Isnin","Selasa","Rabu","Khamis","Jumaat","Sabtu","Ahad"],
     *   "total_booking": [..7 items..]
     * }
     */
    public function weekday()
    {
        $year = (int)$this->input->get('year', true);
        if (!$year) {
            $year = (int)date('Y');
        }

        $this->load->model('Report_model');
        $counts = $this->Report_model->get_weekday_distribution($year);

        $labels = ['Isnin','Selasa','Rabu','Khamis','Jumaat','Sabtu','Ahad'];

        $payload = [
            'year' => $year,
            'labels' => $labels,
            'total_booking' => array_values($counts),
        ];

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($payload));
    }

    /**
     * GET /report/heatmap?year=YYYY
     * Returns day-hour heatmap and busiest slot:
     * {
     *   "year": 2025,
     *   "days": ["Isnin","Selasa","Rabu","Khamis","Jumaat","Sabtu","Ahad"],
     *   "hours": ["00","01",...,"23"],
     *   "matrix": [[...24...], ...7 rows...],
     *   "busiest": { "day": "Rabu", "hour": "15", "count": 12 }
     * }
     */
    public function heatmap()
    {
        $year = (int)$this->input->get('year', true);
        if (!$year) {
            $year = (int)date('Y');
        }

        $this->load->model('Report_model');
        $matrix = $this->Report_model->get_day_hour_heatmap($year);

        $days = ['Isnin','Selasa','Rabu','Khamis','Jumaat','Sabtu','Ahad'];
        $hours = [];
        for ($h = 0; $h < 24; $h++) {
            $hours[] = str_pad((string)$h, 2, '0', STR_PAD_LEFT);
        }

        // Find busiest (max count)
        $max = 0;
        $maxDayIdx = 0;
        $maxHour = 0;
        for ($d = 0; $d < 7; $d++) {
            for ($h = 0; $h < 24; $h++) {
                $val = isset($matrix[$d][$h]) ? (int)$matrix[$d][$h] : 0;
                if ($val > $max) {
                    $max = $val;
                    $maxDayIdx = $d;
                    $maxHour = $h;
                }
            }
        }

        $payload = [
            'year' => $year,
            'days' => $days,
            'hours' => $hours,
            'matrix' => $matrix,
            'busiest' => [
                'day' => $days[$maxDayIdx],
                'hour' => str_pad((string)$maxHour, 2, '0', STR_PAD_LEFT),
                'count' => $max,
            ],
        ];

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($payload));
    }

    /**
     * GET /report/top-therapists?year=YYYY&limit=5
     * Returns top therapists by booking count:
     * {
     *   "year": 2025,
     *   "labels": ["Therapist A", ...],
     *   "total_booking": [10, ...],
     *   "items": [{ "id": 1, "name": "Therapist A", "total_booking": 10 }, ...]
     * }
     */
    public function top_therapists()
    {
        $year = (int)$this->input->get('year', true);
        if (!$year) {
            $year = (int)date('Y');
        }
        $limit = (int)$this->input->get('limit', true) ?: 5;

        $this->load->model('Report_model');
        $rows = $this->Report_model->get_top_therapists($year, $limit);

        $labels = [];
        $counts = [];
        $items = [];
        foreach ($rows as $r) {
            $labels[] = (string)$r->name;
            $counts[] = (int)$r->total_booking;
            $items[] = [
                'id' => (int)$r->id,
                'name' => (string)$r->name,
                'total_booking' => (int)$r->total_booking,
            ];
        }

        $payload = [
            'year' => $year,
            'labels' => $labels,
            'total_booking' => $counts,
            'items' => $items,
        ];

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($payload));
    }

    /**
     * GET /report/top-packages?year=YYYY&limit=5
     * Returns top packages by booking count:
     * {
     *   "year": 2025,
     *   "labels": ["Pakej A", ...],
     *   "total_booking": [12, ...],
     *   "items": [{ "id": 3, "name": "Solo Package A", "total_booking": 12 }, ...]
     * }
     */
    public function top_packages()
    {
        $year = (int)$this->input->get('year', true);
        if (!$year) {
            $year = (int)date('Y');
        }
        $limit = (int)$this->input->get('limit', true) ?: 5;

        $this->load->model('Report_model');
        $rows = $this->Report_model->get_top_packages_by_count($year, $limit);

        $labels = [];
        $counts = [];
        $items = [];
        foreach ($rows as $r) {
            $labels[] = (string)$r->name;
            $counts[] = (int)$r->total_booking;
            $items[] = [
                'id' => (int)$r->id,
                'name' => (string)$r->name,
                'total_booking' => (int)$r->total_booking,
            ];
        }

        $payload = [
            'year' => $year,
            'labels' => $labels,
            'total_booking' => $counts,
            'items' => $items,
        ];

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($payload));
    }
}