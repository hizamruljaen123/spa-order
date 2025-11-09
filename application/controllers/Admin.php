<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Admin Dashboard & Management
 *
 * @property CI_Input $input
 * @property CI_Output $output
 * @property CI_Session $session
 * @property CI_Form_validation $form_validation
 * @property Booking_model $Booking_model
 * @property Therapist_model $Therapist_model
 * @property Package_model $Package_model
 * @property Report_model $Report_model
 * @property Invoice_model $Invoice_model
 * @property Urlcrypt $urlcrypt
 */
class Admin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->load->model(['Booking_model', 'Therapist_model', 'Package_model', 'Report_model', 'Invoice_model']);
        $this->load->helper(['url', 'form']);
        $this->load->library(['session', 'form_validation', 'urlcrypt']);
    
        // Require admin login for all Admin routes
        if (!$this->session->userdata('admin_logged_in')) {
            redirect('login');
        }
    }

    // 1. Dashboard
    public function index()
    {
        $summary = $this->Report_model->get_dashboard_summary();
        $popular = $this->Package_model->get_popular_packages(5);

        $data = [
            'title'   => 'Admin Dashboard',
            'summary' => $summary,
            'popular' => $popular,
            'flash'   => [
                'success' => $this->session->flashdata('success'),
                'error'   => $this->session->flashdata('error'),
            ],
        ];
        $this->load->view('admin/dashboard', $data);
    }

    // 2. Therapists - List
    public function therapists()
    {
        $therapists = $this->Therapist_model->get_all(false);
        // attach URL-safe token for each therapist
        if (is_array($therapists)) {
            foreach ($therapists as $t) {
                if (is_object($t) && isset($t->id)) {
                    $t->token = $this->urlcrypt->encode((int)$t->id) ?: (string)$t->id;
                }
            }
        }
        $data = [
            'title'      => 'Data Therapist',
            'therapists' => $therapists,
            'flash'      => [
                'success' => $this->session->flashdata('success'),
                'error'   => $this->session->flashdata('error'),
            ],
        ];
        $this->load->view('admin/therapist_list', $data);
    }

    // 2a. Therapist Create (POST)
    public function therapist_create()
    {
        if ($this->input->method(true) !== 'POST') {
            redirect('admin/therapists');
            return;
        }

        $this->form_validation->set_rules('name', 'Nama', 'required|trim|min_length[2]');
        $this->form_validation->set_rules('phone', 'No HP', 'trim');
        $this->form_validation->set_rules('status', 'Status', 'required|in_list[available,busy,off]');

        if (!$this->form_validation->run()) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('admin/therapists');
            return;
        }

        $payload = [
            'name'   => $this->input->post('name', true),
            'phone'  => $this->input->post('phone', true),
            'status' => $this->input->post('status', true),
        ];
        $this->Therapist_model->create($payload);
        $this->session->set_flashdata('success', 'Therapist berhasil ditambahkan.');
        redirect('admin/therapists');
    }

    // 2b. Therapist Edit (GET shows in list via query, POST saves)
    public function therapist_edit($token)
    {
        // Decode encrypted token or allow legacy numeric
        $id = ctype_digit((string)$token) ? (int)$token : $this->urlcrypt->decode($token);
        if (!$id) {
            $this->session->set_flashdata('error', 'Parameter tidak valid.');
            redirect('admin/therapists');
            return;
        }

        if ($this->input->method(true) === 'POST') {
            $this->form_validation->set_rules('name', 'Nama', 'required|trim|min_length[2]');
            $this->form_validation->set_rules('phone', 'No HP', 'trim');
            $this->form_validation->set_rules('status', 'Status', 'required|in_list[available,busy,off]');

            if (!$this->form_validation->run()) {
                $this->session->set_flashdata('error', validation_errors());
                redirect('admin/therapists');
                return;
            }

            $payload = [
                'name'   => $this->input->post('name', true),
                'phone'  => $this->input->post('phone', true),
                'status' => $this->input->post('status', true),
            ];
            $this->Therapist_model->update($id, $payload);
            $this->session->set_flashdata('success', 'Therapist berhasil diupdate.');
            redirect('admin/therapists');
            return;
        }

        // GET: Render list with edit target id (view can handle inline edit)
        $therapists = $this->Therapist_model->get_all(false);
        $data = [
            'title'        => 'Data Therapist',
            'therapists'   => $therapists,
            'editItemId'   => $id,
            'editItem'     => $this->Therapist_model->get_by_id($id),
            'flash'        => [
                'success' => $this->session->flashdata('success'),
                'error'   => $this->session->flashdata('error'),
            ],
        ];
        $this->load->view('admin/therapist_list', $data);
    }

    // 2c. Therapist Delete
    public function therapist_delete($token)
    {
        $id = ctype_digit((string)$token) ? (int)$token : $this->urlcrypt->decode($token);
        if (!$id) {
            $this->session->set_flashdata('error', 'Parameter tidak valid.');
            redirect('admin/therapists');
            return;
        }
        $this->Therapist_model->delete($id);
        $this->session->set_flashdata('success', 'Therapist dihapus.');
        redirect('admin/therapists');
    }

    // 3. Packages - List
    public function packages()
    {
        $packages = $this->Package_model->get_all();
        if (is_array($packages)) {
            foreach ($packages as $p) {
                if (is_object($p) && isset($p->id)) {
                    $p->token = $this->urlcrypt->encode((int)$p->id) ?: (string)$p->id;
                }
            }
        }
        $data = [
            'title'    => 'Data Paket',
            'packages' => $packages,
            'flash'    => [
                'success' => $this->session->flashdata('success'),
                'error'   => $this->session->flashdata('error'),
            ],
        ];
        $this->load->view('admin/package_list', $data);
    }

    public function package_create()
    {
        if ($this->input->method(true) !== 'POST') {
            redirect('admin/packages');
            return;
        }

        // New schema: name, category, hands (1/2), duration, price_in_call, price_out_call, currency, description
        $this->form_validation->set_rules('name', 'Nama Paket', 'required|trim|min_length[2]');
        $this->form_validation->set_rules('category', 'Kategori', 'required|trim');
        $this->form_validation->set_rules('hands', 'Jumlah Therapist', 'required|in_list[1,2]');
        $this->form_validation->set_rules('duration', 'Durasi', 'required|integer');
        $this->form_validation->set_rules('price_in_call', 'Harga In Call', 'required|numeric');
        $this->form_validation->set_rules('price_out_call', 'Harga Out Call', 'required|numeric');
        $this->form_validation->set_rules('currency', 'Mata Uang', 'required|trim|max_length[10]');
        $this->form_validation->set_rules('description', 'Deskripsi', 'trim');

        if (!$this->form_validation->run()) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('admin/packages');
            return;
        }

        $payload = [
            'name'           => $this->input->post('name', true),
            'category'       => $this->input->post('category', true),
            'hands'          => (int)$this->input->post('hands', true),
            'duration'       => (int)$this->input->post('duration', true),
            'price_in_call'  => (float)$this->input->post('price_in_call', true),
            'price_out_call' => (float)$this->input->post('price_out_call', true),
            'currency'       => $this->input->post('currency', true) ?: 'RM',
            'description'    => $this->input->post('description', true),
        ];
        $this->Package_model->create($payload);
        $this->session->set_flashdata('success', 'Paket berhasil ditambahkan.');
        redirect('admin/packages');
    }

    public function package_edit($token)
    {
        $id = ctype_digit((string)$token) ? (int)$token : $this->urlcrypt->decode($token);
        if (!$id) {
            $this->session->set_flashdata('error', 'Parameter tidak valid.');
            redirect('admin/packages');
            return;
        }

        if ($this->input->method(true) === 'POST') {
            // New schema validation
            $this->form_validation->set_rules('name', 'Nama Paket', 'required|trim|min_length[2]');
            $this->form_validation->set_rules('category', 'Kategori', 'required|trim');
            $this->form_validation->set_rules('hands', 'Jumlah Therapist', 'required|in_list[1,2]');
            $this->form_validation->set_rules('duration', 'Durasi', 'required|integer');
            $this->form_validation->set_rules('price_in_call', 'Harga In Call', 'required|numeric');
            $this->form_validation->set_rules('price_out_call', 'Harga Out Call', 'required|numeric');
            $this->form_validation->set_rules('currency', 'Mata Uang', 'required|trim|max_length[10]');
            $this->form_validation->set_rules('description', 'Deskripsi', 'trim');

            if (!$this->form_validation->run()) {
                $this->session->set_flashdata('error', validation_errors());
                redirect('admin/packages');
                return;
            }

            $payload = [
                'name'           => $this->input->post('name', true),
                'category'       => $this->input->post('category', true),
                'hands'          => (int)$this->input->post('hands', true),
                'duration'       => (int)$this->input->post('duration', true),
                'price_in_call'  => (float)$this->input->post('price_in_call', true),
                'price_out_call' => (float)$this->input->post('price_out_call', true),
                'currency'       => $this->input->post('currency', true) ?: 'RM',
                'description'    => $this->input->post('description', true),
            ];
            $this->Package_model->update($id, $payload);
            $this->session->set_flashdata('success', 'Paket berhasil diupdate.');
            redirect('admin/packages');
            return;
        }

        $packages = $this->Package_model->get_all();
        $data = [
            'title'      => 'Data Paket',
            'packages'   => $packages,
            'editItemId' => $id,
            'editItem'   => $this->Package_model->get_by_id($id),
            'flash'      => [
                'success' => $this->session->flashdata('success'),
                'error'   => $this->session->flashdata('error'),
            ],
        ];
        $this->load->view('admin/package_list', $data);
    }

    public function package_delete($token)
    {
        $id = ctype_digit((string)$token) ? (int)$token : $this->urlcrypt->decode($token);
        if (!$id) {
            $this->session->set_flashdata('error', 'Parameter tidak valid.');
            redirect('admin/packages');
            return;
        }
        $this->Package_model->delete($id);
        $this->session->set_flashdata('success', 'Paket dihapus.');
        redirect('admin/packages');
    }

    // 4. Schedule / Calendar (FullCalendar)
    // - If AJAX (or has start/end params): returns JSON events
    // - Else: render calendar view
    public function schedule()
    {
        $start = $this->input->get('start', true);
        $end   = $this->input->get('end', true);
        $therapist_id = $this->input->get('therapist_id', true);

        if ($start && $end) {
            $events = $this->Booking_model->get_calendar_events($start, $end, $therapist_id ? (int)$therapist_id : null);
            // encode event ids to tokens for safer URLs
            if (is_array($events)) {
                foreach ($events as &$ev) {
                    if (isset($ev['id'])) {
                        $ev['id'] = $this->urlcrypt->encode((int)$ev['id']) ?: (string)$ev['id'];
                    }
                }
                unset($ev);
            }
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($events));
            return;
        }

        $data = [
            'title'      => 'Kalender Jadwal',
            'therapists' => $this->Therapist_model->get_all(false),
        ];
        $this->load->view('admin/schedule_calendar', $data);
    }

    // 5. Report page (Chart.js)
    public function report()
    {
        $year = (int)$this->input->get('year', true) ?: (int)date('Y');
        $stats = $this->Report_model->get_monthly_stats($year);

        $data = [
            'title' => 'Laporan Pendapatan',
            'year'  => $year,
            'stats' => $stats,
        ];
        $this->load->view('admin/report', $data);
    }

    // 6. Invoice generation
    public function invoice($token)
    {
        $id = ctype_digit((string)$token) ? (int)$token : $this->urlcrypt->decode($token);
        if (!$id) {
            show_404();
            return;
        }

        $booking = $this->Booking_model->get_by_id($id);
        if (!$booking) {
            show_404();
            return;
        }

        $invoice = $this->Invoice_model->get_by_booking($id);
        if (!$invoice) {
            // Not generated yet, show page with a button to generate
            $data = [
                'title'         => 'Invoice',
                'booking'       => $booking,
                'invoice'       => null,
                'booking_token' => ($this->urlcrypt->encode($id) ?: (string)$id),
            ];
        } else {
            $data = [
                'title'         => 'Invoice',
                'booking'       => $booking,
                'invoice'       => $invoice,
                'booking_token' => ($this->urlcrypt->encode($id) ?: (string)$id),
            ];
        }

        // Simple invoice view (HTML); separate PDF generation endpoint:
        $this->load->view('admin/invoice_pdf', $data);
    }

    public function generate_invoice($token)
    {
        $id = ctype_digit((string)$token) ? (int)$token : $this->urlcrypt->decode($token);
        if (!$id) {
            $this->session->set_flashdata('error', 'Parameter tidak valid.');
            redirect('admin');
            return;
        }

        // Create invoice (idempotent)
        $invoice_id = $this->Invoice_model->create_for_booking($id);
        $tokenEnc = $this->urlcrypt->encode($id) ?: (string)$id;

        if (!$invoice_id) {
            $this->session->set_flashdata('error', 'Gagal membuat invoice.');
            redirect('admin/invoice/' . $tokenEnc);
            return;
        }

        $invoice = $this->Invoice_model->get_by_id($invoice_id);
        $booking = $this->Booking_model->get_by_id($id);
        if (!$booking || !$invoice) {
            $this->session->set_flashdata('error', 'Data invoice/booking tidak ditemukan.');
            redirect('admin/invoice/' . $tokenEnc);
            return;
        }

        // Serve plain HTML and trigger the browser print dialog (no Dompdf)
        $html = $this->load->view(
            'admin/invoice_pdf',
            ['booking' => $booking, 'invoice' => $invoice, 'title' => 'Invoice'],
            true
        );

        // Inject auto-print so user can Save as PDF via browser
        if (stripos($html, '</body>') !== false) {
            $html = str_ireplace('</body>', "<script>window.print();</script></body>", $html);
        } else {
            $html .= "<script>window.print();</script>";
        }

        $this->output
            ->set_content_type('text/html')
            ->set_output($html);
    }

    // 7. Booking status actions
    public function booking_confirm($token)
    {
        $id = ctype_digit((string)$token) ? (int)$token : $this->urlcrypt->decode($token);
        if (!$id) {
            $this->session->set_flashdata('error', 'Parameter tidak valid.');
            redirect('admin/schedule');
            return;
        }

        $booking = $this->Booking_model->get_by_id($id);
        if (!$booking) {
            $this->session->set_flashdata('error', 'Booking tidak ditemukan.');
            redirect('admin/schedule');
            return;
        }

        if (!$this->Booking_model->mark_confirmed($id)) {
            $this->session->set_flashdata('error', 'Gagal mengonfirmasi booking.');
            redirect('admin/schedule');
            return;
        }

        // Auto-generate invoice on confirmation (DP by default)
        $invoice_id = $this->Invoice_model->create_for_booking($id, 'DP');
        if ($invoice_id) {
            $inv = $this->Invoice_model->get_by_id($invoice_id);
            $this->session->set_flashdata('success', 'Booking dikonfirmasi. Invoice ' . ($inv ? $inv->invoice_number : '') . ' dibuat.');
        } else {
            $this->session->set_flashdata('success', 'Booking dikonfirmasi. (Invoice gagal dibuat)');
        }

        // Redirect to invoice page so admin can download/print
        $tokenEnc = $this->urlcrypt->encode($id) ?: (string)$id;
        redirect('admin/invoice/' . $tokenEnc);
    }

    public function booking_complete($token)
    {
        $id = ctype_digit((string)$token) ? (int)$token : $this->urlcrypt->decode($token);
        if (!$id) {
            $this->session->set_flashdata('error', 'Parameter tidak valid.');
            redirect('admin/schedule');
            return;
        }

        $booking = $this->Booking_model->get_by_id($id);
        if (!$booking) {
            $this->session->set_flashdata('error', 'Booking tidak ditemukan.');
            redirect('admin/schedule');
            return;
        }

        if (!$this->Booking_model->mark_completed($id)) {
            $this->session->set_flashdata('error', 'Gagal menyelesaikan booking.');
            redirect('admin/schedule');
            return;
        }

        // Optional: set invoice to Lunas when booking completed
        $invoice = $this->Invoice_model->get_by_booking($id);
        if ($invoice) {
            $this->Invoice_model->update_payment_status((int)$invoice->id, 'Lunas');
        }

        $this->session->set_flashdata('success', 'Booking ditandai selesai dan invoice ditandai Lunas.');
        redirect('admin/schedule');
    }

    public function booking_cancel($token)
    {
        $id = ctype_digit((string)$token) ? (int)$token : $this->urlcrypt->decode($token);
        if (!$id) {
            $this->session->set_flashdata('error', 'Parameter tidak valid.');
            redirect('admin/schedule');
            return;
        }

        $booking = $this->Booking_model->get_by_id($id);
        if (!$booking) {
            $this->session->set_flashdata('error', 'Booking tidak ditemukan.');
            redirect('admin/schedule');
            return;
        }

        if (!$this->Booking_model->cancel($id)) {
            $this->session->set_flashdata('error', 'Gagal membatalkan booking.');
            redirect('admin/schedule');
            return;
        }

        $this->session->set_flashdata('success', 'Booking dibatalkan.');
        redirect('admin/schedule');
    }
}