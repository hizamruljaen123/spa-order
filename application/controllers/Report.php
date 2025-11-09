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
}