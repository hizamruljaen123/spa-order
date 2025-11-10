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
 * @property Settings_model $Settings_model
 * @property Urlcrypt $urlcrypt
 */
class Admin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->load->model(['Booking_model', 'Therapist_model', 'Package_model', 'Report_model', 'Invoice_model', 'Settings_model']);
        $this->load->helper(['url', 'form']);
        $this->load->library(['session', 'form_validation', 'urlcrypt']);
        // Bootstrap settings storage table/keys
        $this->Settings_model->ensure_bootstrap();
    
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

    // 4b. Bookings list (table with filters)
    public function bookings()
    {
        $filters = [
            'from'         => $this->input->get('from', true),
            'to'           => $this->input->get('to', true),
            'status'       => $this->input->get('status', true),
            'therapist_id' => $this->input->get('therapist_id', true),
            'package_id'   => $this->input->get('package_id', true),
        ];

        if (isset($filters['therapist_id']) && $filters['therapist_id'] !== '') {
            $filters['therapist_id'] = (int)$filters['therapist_id'];
        } else {
            unset($filters['therapist_id']);
        }
        if (isset($filters['package_id']) && $filters['package_id'] !== '') {
            $filters['package_id'] = (int)$filters['package_id'];
        } else {
            unset($filters['package_id']);
        }
        if (isset($filters['status']) && $filters['status'] === '') {
            unset($filters['status']);
        }

        $bookings = $this->Booking_model->get_all($filters, 200, 0);

        if (is_array($bookings)) {
            foreach ($bookings as $b) {
                if (is_object($b) && isset($b->id)) {
                    $b->token = $this->urlcrypt->encode((int)$b->id) ?: (string)$b->id;
                }
            }
        }

        $data = [
            'title'      => 'Daftar Booking',
            'bookings'   => $bookings,
            'filters'    => $filters,
            'therapists' => $this->Therapist_model->get_all(false),
            'packages'   => $this->Package_model->get_all(),
            'flash'      => [
                'success' => $this->session->flashdata('success'),
                'error'   => $this->session->flashdata('error'),
            ],
        ];
        $this->load->view('admin/booking_list', $data);
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

    /**
     * AJAX: Set booking status (accepted/rejected/working/completed/canceled/pending)
     * POST: token (encrypted or numeric), status (string)
     * Returns JSON: { ok: true, status: "<new_status>" }
     */
    public function booking_set_status()
    {
        if ($this->input->method(true) !== 'POST') {
            $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode(['ok' => false, 'error' => 'Method Not Allowed']));
            return;
        }

        $token  = $this->input->post('token', true);
        $status = strtolower(trim((string)$this->input->post('status', true)));

        // Normalize Indonesian aliases to canonical internal statuses
        $aliases = [
            'diterima'  => 'accepted',
            'terima'    => 'accepted',
            'confirm'   => 'accepted',
            'confirmed' => 'accepted',

            'ditolak'   => 'rejected',
            'tolak'     => 'rejected',
            'reject'    => 'rejected',

            'on_working'=> 'working',
            'onworking' => 'working',
            'working'   => 'working',
            'proses'    => 'working',

            'selesai'   => 'completed',
            'complete'  => 'completed',
            'done'      => 'completed',

            'batal'     => 'canceled',
            'cancel'    => 'canceled',

            'baru'      => 'pending',
            'pending'   => 'pending',
        ];
        if (isset($aliases[$status])) {
            $status = $aliases[$status];
        }

        $allowed = ['pending', 'accepted', 'working', 'completed', 'rejected', 'canceled'];
        if (!in_array($status, $allowed, true)) {
            $this->output
                ->set_status_header(422)
                ->set_content_type('application/json')
                ->set_output(json_encode(['ok' => false, 'error' => 'Status tidak dikenali']));
            return;
        }

        $id = $token && !ctype_digit((string)$token) ? $this->urlcrypt->decode($token) : (int)$token;
        if (!$id) {
            $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode(['ok' => false, 'error' => 'Invalid token']));
            return;
        }

        // Ensure booking exists
        $booking = $this->Booking_model->get_by_id($id);
        if (!$booking) {
            $this->output
                ->set_status_header(404)
                ->set_content_type('application/json')
                ->set_output(json_encode(['ok' => false, 'error' => 'Booking tidak ditemukan']));
            return;
        }

        // Persist status
        $ok = $this->Booking_model->update($id, ['status' => $status]);

        // Side-effects:
        // - When accepted: auto-create invoice (DP)
        // - When completed: mark invoice Lunas
        if ($ok) {
            if ($status === 'accepted') {
                $this->Invoice_model->create_for_booking($id, 'DP');
            } elseif ($status === 'completed') {
                $inv = $this->Invoice_model->get_by_booking($id);
                if ($inv) {
                    $this->Invoice_model->update_payment_status((int)$inv->id, 'Lunas');
                }
            }
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['ok' => (bool)$ok, 'status' => $status]));
    }
    
    // AJAX: Update booking date/time (drag-and-drop or manual edit)
    public function booking_update_time()
    {
        if ($this->input->method(true) !== 'POST') {
            $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode(['ok' => false, 'error' => 'Method Not Allowed']));
            return;
        }
    
        $token = $this->input->post('token', true);
        $date  = $this->input->post('date', true);
        $time  = $this->input->post('time', true);
    
        $id = $token && !ctype_digit((string)$token) ? $this->urlcrypt->decode($token) : (int)$token;
        if (!$id) {
            $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode(['ok' => false, 'error' => 'Invalid token']));
            return;
        }
    
        // Normalize and validate date/time
        if (preg_match('/^\d{2}:\d{2}$/', (string)$time)) {
            $time .= ':00';
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)$date) || !preg_match('/^\d{2}:\d{2}:\d{2}$/', (string)$time)) {
            $this->output
                ->set_status_header(422)
                ->set_content_type('application/json')
                ->set_output(json_encode(['ok' => false, 'error' => 'Invalid date/time format']));
            return;
        }
    
        $ok = $this->Booking_model->update((int)$id, ['date' => $date, 'time' => $time]);
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['ok' => (bool)$ok]));
    }
    
    // AJAX: Delete booking
    public function booking_delete()
    {
        if ($this->input->method(true) !== 'POST') {
            $this->output
                ->set_status_header(405)
                ->set_content_type('application/json')
                ->set_output(json_encode(['ok' => false, 'error' => 'Method Not Allowed']));
            return;
        }
    
        $token = $this->input->post('token', true);
        $id = $token && !ctype_digit((string)$token) ? $this->urlcrypt->decode($token) : (int)$token;
        if (!$id) {
            $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode(['ok' => false, 'error' => 'Invalid token']));
            return;
        }
    
        $ok = $this->Booking_model->delete((int)$id);
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['ok' => (bool)$ok]));
    }

    // 8. Settings - Telegram Bot configuration
    public function settings()
    {
        // POST: save settings
        if ($this->input->method(true) === 'POST') {
            $this->form_validation->set_rules('telegram_bot_token', 'Telegram Bot Token', 'required|trim|min_length[20]');
            $this->form_validation->set_rules('telegram_chat_id', 'Telegram Chat ID', 'required|trim|min_length[3]');
            if (!$this->form_validation->run()) {
                $this->session->set_flashdata('error', validation_errors());
                redirect('admin/settings');
                return;
            }

            $bot  = $this->input->post('telegram_bot_token', true);
            $chat = $this->input->post('telegram_chat_id', true);

            $ok = $this->Settings_model->set_many([
                'telegram_bot_token' => $bot,
                'telegram_chat_id'   => $chat,
            ]);

            if ($ok) {
                $this->session->set_flashdata('success', 'Pengaturan Telegram tersimpan.');
            } else {
                $this->session->set_flashdata('error', 'Gagal menyimpan pengaturan.');
            }
            redirect('admin/settings');
            return;
        }

        // GET: render settings page
        $vals = $this->Settings_model->get_many(['telegram_bot_token','telegram_chat_id']);
        $data = [
            'title'    => 'Pengaturan Sistem',
            'settings' => $vals,
            'flash'    => [
                'success' => $this->session->flashdata('success'),
                'error'   => $this->session->flashdata('error'),
            ],
        ];
        $this->load->view('admin/settings', $data);
    }
}