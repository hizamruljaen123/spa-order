<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @property Package_model $Package_model
 * @property Therapist_model $Therapist_model
 * @property Booking_model $Booking_model
 * @property Invoice_model $Invoice_model
 * @property Urlcrypt $urlcrypt
 * @property CI_Session $session
 * @property CI_Form_validation $form_validation
 * @property CI_Input $input
 * @property CI_Output $output
 */

class Booking extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // Load models and libs (most helpers/libs already autoloaded)
        $this->load->model(['Package_model', 'Therapist_model', 'Booking_model', 'Invoice_model']);
        $this->load->helper(['url', 'form']);
        $this->load->library(['session', 'form_validation', 'urlcrypt']);
        date_default_timezone_set('Asia/Jakarta');
    }
public function index()
{
    $data = [
        'title'    => 'Spa Home',
        'packages' => $this->Package_model->get_all(),
    ];

    $this->load->view('booking_home', $data);
}

public function form()
{
    $data = [
        'title'       => 'Spa Booking',
        'packages'    => $this->Package_model->get_all(),
        'therapists'  => $this->Therapist_model->get_all(true),
        'success'     => $this->session->flashdata('success'),
        'error'       => $this->session->flashdata('error'),
        'validation'  => validation_errors()
    ];

    $this->load->view('booking_form', $data);
}


    public function submit()
    {
        // Basic validation rules
        $this->form_validation->set_rules('customer_name', 'Nama', 'required|trim|min_length[2]');
        $this->form_validation->set_rules('address', 'Alamat', 'required|trim|min_length[5]');
        $this->form_validation->set_rules('package_id', 'Package', 'required|integer');
        $this->form_validation->set_rules('date', 'Tanggal', 'required|regex_match[/^\d{4}-\d{2}-\d{2}$/]');
        $this->form_validation->set_rules('time', 'Jam', 'required|regex_match[/^\d{2}:\d{2}(:\d{2})?$/]');
        $this->form_validation->set_rules('call_type', 'Tipe Panggilan', 'required|in_list[IN,OUT]');

        $therapist_id_post = $this->input->post('therapist_id', true);
        if ($therapist_id_post !== null && $therapist_id_post !== '') {
            $this->form_validation->set_rules('therapist_id', 'Therapist', 'integer');
        }

        if ($this->form_validation->run() === FALSE) {
            // Return back to form with errors
            return $this->form();
        }

        // Gather sanitized post data
        $customer_name = $this->input->post('customer_name', true);
        $address       = $this->input->post('address', true);
        $package_id    = (int)$this->input->post('package_id', true);
        $date          = $this->input->post('date', true); // YYYY-MM-DD
        $time          = $this->input->post('time', true); // HH:MM or HH:MM:SS
        $therapist_id  = ($therapist_id_post === null || $therapist_id_post === '') ? null : (int)$therapist_id_post;
        // Normalize call type (IN/OUT)
        $call_type_raw = strtoupper($this->input->post('call_type', true));
        $call_type     = ($call_type_raw === 'OUT') ? 'OUT' : 'IN';

        // Create booking payload
        $payload = [
            'customer_name' => $customer_name,
            'address'       => $address,
            'package_id'    => $package_id,
            'therapist_id'  => $therapist_id,
            'call_type'     => $call_type,
            'date'          => $date,
            'time'          => strlen($time) === 5 ? ($time . ':00') : $time, // normalize to HH:MM:SS
            // total_price will be auto-derived by model if null (uses call_type)
            'total_price'   => null,
            'status'        => 'pending',
            'created_at'    => date('Y-m-d H:i:s'),
        ];

        // Save booking
        $booking_id = $this->Booking_model->create($payload);
        if (!$booking_id) {
            $this->session->set_flashdata('error', 'Gagal menyimpan pemesanan. Silakan coba lagi.');
            redirect('booking/form');
            return;
        }

        // Fetch booking detail with joins for message and success view
        $booking = $this->Booking_model->get_by_id($booking_id);

        // Create invoice (DP) for proof and compute 1-hour validity
        $invoice_id = $this->Invoice_model->create_for_booking($booking_id, 'DP');
        $invoice    = $this->Invoice_model->get_by_booking($booking_id);
        $expires_at = $invoice ? date('Y-m-d H:i:s', strtotime($invoice->created_at.' +1 hour')) : null;

        // Send Telegram notification directly (avoid instantiating another CI controller)
        try {
            $botToken = getenv('TELEGRAM_BOT_TOKEN') ?: '';
            $chatId   = getenv('TELEGRAM_CHAT_ID') ?: '';

            if (!empty($botToken) && !empty($chatId)) {
                $customer = $booking->customer_name ?? '-';
                $address  = $booking->address ?? '-';
                $thera    = isset($booking->therapist_name) && $booking->therapist_name ? $booking->therapist_name : '-';
                $package  = isset($booking->package_name) && $booking->package_name ? $booking->package_name : '-';
                $date     = $booking->date ?? '-';
                $time     = isset($booking->time) ? substr($booking->time, 0, 5) : '-';

                $message = "📋 *SPA BOOKING REQUEST*\n"
                         . "👤 Nama: {$customer}\n"
                         . "🏠 Alamat: {$address}\n"
                         . "💆‍♀️ Therapist: {$thera}\n"
                         . "💅 Paket: {$package}\n"
                         . "📅 Tanggal: {$date}\n"
                         . "⏰ Jam: {$time}";

                $this->_telegram_send($botToken, $chatId, $message);
            } else {
                log_message('error', 'Telegram bot token/chat id not set (env TELEGRAM_BOT_TOKEN / TELEGRAM_CHAT_ID). Skipping Telegram send.');
            }
        } catch (Throwable $e) {
            // Do not block the flow if Telegram fails; log if needed
            log_message('error', 'Telegram notification failed: ' . $e->getMessage());
        }

        // Redirect user to invoice proof page with flash message
        if ($invoice) {
            $this->session->set_flashdata(
                'success',
                'Pesanan berhasil dikirim. Nomor Invoice: '.$invoice->invoice_number.'. Berlaku hingga: '.($expires_at ?: '-').'. Admin akan menghubungi Anda.'
            );
            redirect('booking/invoice/'.$this->urlcrypt->encode($booking_id));
        } else {
            $this->session->set_flashdata('success', 'Pesanan berhasil dikirim. Kami akan segera menghubungi Anda.');
            redirect('booking/form');
        }
    }
 
    /**
     * Send Telegram message using Bot API.
     * @param string $botToken
     * @param string $chatId
     * @param string $message
     * @return void
     */
    private function _telegram_send($botToken, $chatId, $message)
    {
        $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
        $post = [
            'chat_id'    => $chatId,
            'text'       => $message,
            'parse_mode' => 'Markdown'
        ];

        if (!function_exists('curl_init')) {
            log_message('error', 'cURL not available; cannot send Telegram message.');
            return;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        $resp = curl_exec($ch);
        $err  = curl_error($ch);
        $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($resp === false || $http < 200 || $http >= 300) {
            log_message('error', 'Telegram send failed: ' . ($err ?: "HTTP {$http}"));
        }
    }

    /**
     * GET /booking/availability?date=YYYY-MM-DD&therapist_id=ID(optional)
     * Returns JSON of slots, booked, and available times for the selected date (and therapist if provided).
     */
    public function availability()
    {
        $date = $this->input->get('date', true);
        $therapist_id_param = $this->input->get('therapist_id', true);
        $therapist_id = ($therapist_id_param === null || $therapist_id_param === '') ? null : (int)$therapist_id_param;

        // Validate date format
        if (empty($date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode(['error' => 'Invalid date. Expected format YYYY-MM-DD']));
            return;
        }

        // Fetch booked times (HH:MM)
        $booked = $this->Booking_model->get_booked_times($date, $therapist_id);

        // Generate daily slots (make configurable later if needed)
        $slots = $this->generate_time_slots('09:00', '21:00', 60); // hourly slots 09:00..21:00

        // Compute available = slots - booked
        $available = array_values(array_diff($slots, $booked));

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'date'         => $date,
                'therapist_id' => $therapist_id,
                'slots'        => $slots,
                'booked'       => array_values($booked),
                'available'    => $available,
            ]));
    }

    /**
     * Helper: generate time slots between start..end inclusive by interval minutes.
     * @param string $start 'HH:MM'
     * @param string $end   'HH:MM'
     * @param int $intervalMinutes
     * @return array of 'HH:MM'
     */
    private function generate_time_slots($start = '09:00', $end = '21:00', $intervalMinutes = 60)
    {
        $slots = [];
        $startTs = strtotime($start);
        $endTs   = strtotime($end);

        if ($startTs === false || $endTs === false || $intervalMinutes <= 0) {
            return $slots;
        }

        for ($t = $startTs; $t <= $endTs; $t += $intervalMinutes * 60) {
            $slots[] = date('H:i', $t);
        }
        return $slots;
    }

    /**
     * Mobile-first booking page with simplified flow (optional separate page).
     */
    public function mobile()
    {
        $data = [
            'title'       => 'Spa Booking (Mobile)',
            'packages'    => $this->Package_model->get_all(),
            'therapists'  => $this->Therapist_model->get_all(true),
            'success'     => $this->session->flashdata('success'),
            'error'       => $this->session->flashdata('error'),
            'validation'  => validation_errors(),
        ];

        $this->load->view('booking_mobile', $data);
    }

    /**
     * Booking invoice proof page for user
     * GET /booking/invoice/{booking_id}
     */
    public function invoice($booking_token)
    {
        // Support both legacy numeric and encrypted tokens
        $id = null;
        if (ctype_digit((string)$booking_token)) {
            $id = (int)$booking_token;
        } else {
            $id = $this->urlcrypt->decode($booking_token);
        }

        if (!$id) {
            $this->session->set_flashdata('error', 'Link invoice tidak valid.');
            redirect('booking/form');
            return;
        }

        $booking = $this->Booking_model->get_by_id($id);
        $invoice = $this->Invoice_model->get_by_booking($id);

        if (!$booking || !$invoice) {
            $this->session->set_flashdata('error', 'Invoice tidak ditemukan.');
            redirect('booking/form');
            return;
        }

        $expires_at = date('Y-m-d H:i:s', strtotime($invoice->created_at.' +1 hour'));
        $expired    = (time() > strtotime($expires_at));

        $data = [
            'title'      => 'Bukti Pemesanan',
            'booking'    => $booking,
            'invoice'    => $invoice,
            'expires_at' => $expires_at,
            'expired'    => $expired,
            'success'    => $this->session->flashdata('success'),
            'error'      => $this->session->flashdata('error'),
        ];

        $this->load->view('booking_invoice', $data);
    }
}