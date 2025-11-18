<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @property Package_model $Package_model
 * @property Therapist_model $Therapist_model
 * @property Booking_model $Booking_model
 * @property Invoice_model $Invoice_model
 * @property Settings_model $Settings_model
 * @property Addon_model $Addon_model
 * @property Ad_model $Ad_model
 * @property Product_model $Product_model
 * @property Urlcrypt $urlcrypt
 * @property CI_Session $session
 * @property CI_Form_validation $form_validation
 * @property Exclusive_treatment_model $Exclusive_treatment_model
 * @property CI_Input $input
 * @property CI_Output $output
 */

class Booking extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // Load models and libs (most helpers/libs already autoloaded)
        $this->load->model(['Package_model', 'Therapist_model', 'Booking_model', 'Invoice_model', 'Settings_model', 'Addon_model', 'Exclusive_treatment_model']);
        // Ensure settings table/keys exist even if admin hasn't opened settings page yet
        $this->Settings_model->ensure_bootstrap();
        $this->load->helper(['url', 'form']);
        $this->load->library(['session', 'form_validation', 'urlcrypt']);
        date_default_timezone_set('Asia/Jakarta');
    }
public function index()
{
    // Load models
    $this->load->model('Ad_model');
    $this->load->model('Product_model');

    // Get all active ads for slider
    $active_ads = $this->Ad_model->get_active_ads();

    // Get all active products for grid display
    $active_products = $this->Product_model->get_active_products();

    // Get slider interval from settings
    $slider_interval = $this->Settings_model->get('ad_slider_interval', 2); // Default 2 seconds

    $data = [
        'title'    => 'Spa Booking',
        'packages' => $this->Package_model->get_all(),
        'active_ads' => $active_ads,
        'active_products' => $active_products,
        'exclusive_treatments_grouped' => $this->Exclusive_treatment_model->get_treatments_grouped(),
        'slider_interval' => (int)$slider_interval * 1000, // Convert to milliseconds for JS
    ];

    $this->load->view('booking_home', $data);
}

public function form()
{
    // Optional selected package from query string (?package_id=ID)
    $selected_param = $this->input->get('package_id', true);
    $selected_package_id = (isset($selected_param) && $selected_param !== '' && ctype_digit((string)$selected_param))
        ? (int)$selected_param
        : null;

    // Load models
    $this->load->model('Exclusive_treatment_model');

    // Get packages from both sources: regular packages and exclusive treatments
    $regular_packages = $this->Package_model->get_all();
    $exclusive_treatments = $this->Exclusive_treatment_model->get_main_treatments_as_packages();
    $combined_packages = array_merge($regular_packages, $exclusive_treatments);

    // Get add-ons from both sources: regular add-ons and exclusive add-ons
    $regular_addons = $this->Addon_model->get_active_grouped();
    $exclusive_addons = $this->Exclusive_treatment_model->get_addon_treatments();

    // Group exclusive add-ons by category
    $exclusive_addons_grouped = [];
    foreach ($exclusive_addons as $addon) {
        $category = $addon->category;
        if (!isset($exclusive_addons_grouped[$category])) {
            $exclusive_addons_grouped[$category] = [];
        }
        $exclusive_addons_grouped[$category][] = $addon;
    }

    // Combine regular and exclusive add-ons
    $addons_grouped = $regular_addons;
    foreach ($exclusive_addons_grouped as $category => $items) {
        if (!isset($addons_grouped[$category])) {
            $addons_grouped[$category] = [];
        }
        $addons_grouped[$category] = array_merge($addons_grouped[$category], $items);
    }

    $data = [
        'title'                => 'Spa Booking',
        'packages'             => $combined_packages,
        'therapists'           => $this->Therapist_model->get_all(true),
        'success'              => $this->session->flashdata('success'),
        'error'                => $this->session->flashdata('error'),
        'validation'           => validation_errors(),
        'selected_package_id'  => $selected_package_id,
        'addons_grouped'       => $addons_grouped,
    ];

    // Render booking form view
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
        // Optional customer phone to include WhatsApp link on notification
        $this->form_validation->set_rules('phone', 'No Telefon', 'trim|max_length[20]');

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
        $time          = $this->input->post('time', true); // HH:MM:SS
        $therapist_id  = ($therapist_id_post === null || $therapist_id_post === '') ? null : (int)$therapist_id_post;
        // Optional phone (not stored) for WhatsApp link in notification
        $phone         = $this->input->post('phone', true);
        // Normalize call type (IN/OUT)
        $call_type_raw = strtoupper($this->input->post('call_type', true));
        $call_type     = ($call_type_raw === 'OUT') ? 'OUT' : 'IN';

        // Check availability before proceeding
        $time_hhmm = substr($time, 0, 5); // Extract HH:MM from HH:MM:SS
        $booked_times = $this->Booking_model->get_booked_times($date, $therapist_id);
        if (in_array($time_hhmm, $booked_times)) {
            $this->session->set_flashdata('error', 'Jam yang dipilih sudah dipesan. Silakan pilih jam lain.');
            redirect('booking/form');
            return;
        }

        // Parse selected add-ons (CSV of IDs)
        $addon_ids_csv = (string)$this->input->post('addon_ids', true);
        $addon_ids = [];
        if ($addon_ids_csv !== '') {
            $parts = preg_split('/\s*,\s*/', $addon_ids_csv);
            foreach ($parts as $pid) {
                if ($pid !== '' && ctype_digit((string)$pid)) {
                    $addon_ids[] = (int)$pid;
                }
            }
            $addon_ids = array_values(array_unique($addon_ids));
        }

        // Load models for handling exclusive treatments
        $this->load->model('Exclusive_treatment_model');

        // Handle package selection - check if it's an exclusive treatment or regular package
        $base = 0.0;
        $pkg = null;
        $treatment = null;

        if (strpos($package_id, 'treatment_') === 0) {
            // This is an exclusive treatment
            $treatment_id = str_replace('treatment_', '', $package_id);
            $treatment = $this->Exclusive_treatment_model->get_treatment_by_id($treatment_id);
            if ($treatment) {
                $base = (float)$treatment['price'];
            }
        } else {
            // This is a regular package
            $pkg = $this->Package_model->get_by_id($package_id);
            if ($pkg) {
                $base = ($call_type === 'OUT') ? (float)$pkg->price_out_call : (float)$pkg->price_in_call;
            }
        }

        // Compute add-ons total
        $addons_total = 0.0;
        $regular_addon_ids = [];
        $exclusive_addon_ids = [];

        if (!empty($addon_ids)) {
            foreach ($addon_ids as $addon_id) {
                if (strpos($addon_id, 'treatment_') === 0) {
                    // Exclusive addon
                    $exclusive_addon_ids[] = str_replace('treatment_', '', $addon_id);
                } else {
                    // Regular addon
                    $regular_addon_ids[] = $addon_id;
                }
            }

            // Get total for regular add-ons
            if (!empty($regular_addon_ids)) {
                $addons_total += (float)$this->Addon_model->get_total_for_ids($regular_addon_ids);
            }

            // Get total for exclusive add-ons
            if (!empty($exclusive_addon_ids)) {
                foreach ($exclusive_addon_ids as $ex_id) {
                    $ex_addon = $this->Exclusive_treatment_model->get_treatment_by_id($ex_id);
                    if ($ex_addon) {
                        $addons_total += (float)$ex_addon['price'];
                    }
                }
            }
        }

        $total_price = round(((float)$base + (float)$addons_total), 2);

        // Create booking payload
        $payload = [
            'customer_name' => $customer_name,
            'address'       => $address,
            'package_id'    => $package_id,
            'therapist_id'  => $therapist_id,
            'call_type'     => $call_type,
            'date'          => $date,
            'time'          => strlen($time) === 5 ? ($time . ':00') : $time, // normalize to HH:MM:SS
            'total_price'   => $total_price,
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

        // Attach add-ons to booking (qty=1 each)
        if (!empty($addon_ids)) {
            $this->Addon_model->attach_to_booking((int)$booking_id, $addon_ids);
        }

        // Fetch booking detail with joins for message and success view
        $booking = $this->Booking_model->get_by_id($booking_id);

        // Create invoice (DP) for proof and compute 1-hour validity
        $invoice_id = $this->Invoice_model->create_for_booking($booking_id, 'DP');
        $invoice    = $this->Invoice_model->get_by_booking($booking_id);
        $expires_at = $invoice ? date('Y-m-d H:i:s', strtotime($invoice->created_at.' +1 hour')) : null;

        // Send Telegram notification directly (avoid instantiating another CI controller)
        try {
            // Prefer configured values from settings; fallback to environment variables
            $botToken = $this->Settings_model->get('telegram_bot_token', '');
            $chatId   = $this->Settings_model->get('telegram_chat_id', '');
            if (empty($botToken) || empty($chatId)) {
                $botToken = $botToken ?: (getenv('TELEGRAM_BOT_TOKEN') ?: '');
                $chatId   = $chatId ?: (getenv('TELEGRAM_CHAT_ID') ?: '');
            }
            
            if (!empty($botToken) && !empty($chatId)) {
                $customer = $booking->customer_name ?? '-';
                $address  = $booking->address ?? '-';
                $thera    = isset($booking->therapist_name) && $booking->therapist_name ? $booking->therapist_name : '-';
                $package  = isset($booking->package_name) && $booking->package_name ? $booking->package_name : '-';
                $date     = $booking->date ?? '-';
                $time     = isset($booking->time) ? substr($booking->time, 0, 5) : '-';

                // Construct neat Markdown message with WhatsApp link and form URL
                $formUrl = site_url('booking/form');
                $phoneSan = $phone ? preg_replace('/\D+/', '', (string)$phone) : '';

                // Package/Treatment name - handle exclusive treatments
                if (strpos($payload['package_id'], 'treatment_') === 0) {
                    $treatment_id = str_replace('treatment_', '', $payload['package_id']);
                    $ex_treatment = $this->Exclusive_treatment_model->get_treatment_by_id($treatment_id);
                    $package = $ex_treatment ? $ex_treatment['name'] : '-';
                } else {
                    $package = isset($booking->package_name) && $booking->package_name ? $booking->package_name : '-';
                }

                // Add-ons list for Telegram (handle both regular and exclusive add-ons)
                $addon_text = '-';
                $exclusive_addon_details = [];

                if (!empty($addon_ids)) {
                    $rows_map = $this->Addon_model->get_by_ids($regular_addon_ids); // Only regular add-ons for display
                    $names = [];

                    // Add regular add-ons
                    foreach ($regular_addon_ids as $aid) {
                        if (isset($rows_map[$aid])) {
                            $r = $rows_map[$aid];
                            $names[] = ($r->name ?? '-') . ' (' . (($r->currency ?? 'RM')) . ' ' . number_format((float)($r->price ?? 0), 0, ',', '.') . ')';
                        }
                    }

                    // Add exclusive add-ons and collect details
                    foreach ($exclusive_addon_ids as $ex_id) {
                        $ex_addon = $this->Exclusive_treatment_model->get_treatment_by_id($ex_id);
                        if ($ex_addon) {
                            $addon_name = "\n*".($ex_addon['name'] ?? '-') . ' (' . (($ex_addon['currency'] ?? 'RM')) . ' ' . number_format((float)($ex_addon['price'] ?? 0), 0, ',', '.') . ')';
                            $names[] = $addon_name;
                            $exclusive_addon_details[] = $ex_addon;
                        }
                    }

                    if (!empty($names)) {
                        $addon_text = implode(', ', $names);
                    }
                }

                // Totals formatting (use package/booking currency)
                $currency = isset($booking->currency) ? (string)$booking->currency : 'RM';
                $addons_total_fmt = $addons_total ? ($currency . ' ' . number_format((float)$addons_total, 0, ',', '.')) : '-';
                $total_fmt = $currency . ' ' . number_format((float)($booking->total_price ?? $total_price), 0, ',', '.');

                $invNo = ($invoice && isset($invoice->invoice_number)) ? $invoice->invoice_number : '-';
                // Add exclusive treatment and add-on details if any
                $exclusive_info = "";
                if (strpos($payload['package_id'], 'treatment_') === 0) {
                    $exclusive_info .= "\n🔸 *Rawatan Eksklusif*: Ya";
                }
                if (!empty($exclusive_addon_details)) {
                    $exclusive_info .= "\n🔸 *Add-on Eksklusif*: " . count($exclusive_addon_details) . " item";
                }

                $message = "*SPA BOOKING REQUEST*\n\n"
                         . "👤 *Nama*: {$customer}\n\n"
                         . "🏠 *Alamat*: {$address}\n\n"
                         . "💅 *Paket*: {$package}\n\n"
                         . "➕ *Add-on*: {$addon_text}\n\n"
                         . "‍♀️ *Terapis*: {$thera}\n\n"
                         . "📅 *Tanggal*: {$date}\n\n"
                         . "⏰ *Jam*: {$time}\n\n"
                         . "🧾 *Invoice*: {$invNo}\n"
                         . "💵 *Tambahan Add-on*: {$addons_total_fmt}\n"
                         . "💰 *Total*: {$total_fmt}\n{$exclusive_info}\n\n"
                         . "📞 *Telefon*: " . ($phoneSan ? "[{$phone}](https://wa.me/{$phoneSan})" : "-") . "\n"
                         . "\n[📄 Buka Borang Tempahan]({$formUrl})";

                $this->_telegram_send($botToken, $chatId, $message);
            } else {
                log_message('error', 'Telegram bot token/chat id not set (env TELEGRAM_BOT_TOKEN / TELEGRAM_CHAT_ID). Skipping Telegram send.');
            }
        } catch (Throwable $e) {
            // Do not block the flow if Telegram fails; log if needed
            log_message('error', 'Telegram notification failed: ' . $e->getMessage());
        }

        // Redirect user to Success Booking page with flash message
        if ($invoice) {
            $this->session->set_flashdata(
                'success',
                'Pesanan berhasil dikirim. Nomor Invoice: '.$invoice->invoice_number.'. Berlaku hingga: '.($expires_at ?: '-').'. Admin akan menghubungi Anda.'
            );
        } else {
            $this->session->set_flashdata('success', 'Pesanan berhasil dikirim. Kami akan segera menghubungi Anda.');
        }
        redirect('booking/success/'.$this->urlcrypt->encode($booking_id));
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

        $this->load->view('booking_home', $data);
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

        // Get package/therapy name based on booking
        $package_name = isset($booking->package_name) ? $booking->package_name : '-';
   
        // If it's an exclusive treatment, get the name from the model
        if ($booking && isset($booking->package_id) && strpos($booking->package_id, 'treatment_') === 0) {
            $this->load->model('Exclusive_treatment_model');
            $treatment_id = str_replace('treatment_', '', $booking->package_id);
            $treatment = $this->Exclusive_treatment_model->get_treatment_by_id($treatment_id);
            if ($treatment) {
                $package_name = $treatment['name'];
            }
        }
   
        $data = [
            'title'      => 'Bukti Pemesanan',
            'booking'    => $booking,
            'invoice'    => $invoice,
            'expires_at' => $expires_at,
            'expired'    => $expired,
            'package_name' => $package_name,
            'success'    => $this->session->flashdata('success'),
            'error'      => $this->session->flashdata('error'),
        ];

        $this->load->view('booking_invoice', $data);
    }

    /**
     * Success booking page with WhatsApp contact and auto-redirect after 5 minutes.
     * GET /booking/success/{booking_id}
     */
    public function success($booking_token)
    {
        // Support both legacy numeric and encrypted tokens
        $id = null;
        if (ctype_digit((string)$booking_token)) {
            $id = (int)$booking_token;
        } else {
            $id = $this->urlcrypt->decode($booking_token);
        }

        if (!$id) {
            $this->session->set_flashdata('error', 'Link success booking tidak valid.');
            redirect('booking/form');
            return;
        }

        $booking = $this->Booking_model->get_by_id($id);
        $invoice = $this->Invoice_model->get_by_booking($id);

        if (!$booking) {
            $this->session->set_flashdata('error', 'Data booking tidak ditemukan.');
            redirect('booking/form');
            return;
        }

        // Get package/therapy name based on booking
        $package_name = isset($booking->package_name) ? $booking->package_name : '-';
   
        // If it's an exclusive treatment, get the name from the model
        if ($booking && isset($booking->package_id) && strpos($booking->package_id, 'treatment_') === 0) {
            $this->load->model('Exclusive_treatment_model');
            $treatment_id = str_replace('treatment_', '', $booking->package_id);
            $treatment = $this->Exclusive_treatment_model->get_treatment_by_id($treatment_id);
            if ($treatment) {
                $package_name = $treatment['name'];
            }
        }
   
        $tokenEnc = $this->urlcrypt->encode($id) ?: (string)$id;
   
        $data = [
            'title'     => 'Pemesanan Berhasil',
            'booking'   => $booking,
            'invoice'   => $invoice,
            'package_name' => $package_name,
            'tokenEnc'  => $tokenEnc,
            'success'   => $this->session->flashdata('success'),
            'error'     => $this->session->flashdata('error'),
        ];

        $this->load->view('booking_success', $data);
    }
}
