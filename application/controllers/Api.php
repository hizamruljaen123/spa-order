<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @property CI_Input $input
 * @property CI_Output $output
 * @property Settings_model $Settings_model
 */

class Api extends CI_Controller
{
    // Credentials populated from database settings (app_settings)
    private $botToken = '';
    private $chatId   = '';

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->load->helper('security');

        // Load settings from database for Telegram credentials
        $this->load->model('Settings_model');
        $this->Settings_model->ensure_bootstrap();
        $vals = $this->Settings_model->get_many(['telegram_bot_token','telegram_chat_id']);
        $this->botToken = !empty($vals['telegram_bot_token']) ? (string)$vals['telegram_bot_token'] : (getenv('TELEGRAM_BOT_TOKEN') ?: '');
        $this->chatId   = !empty($vals['telegram_chat_id']) ? (string)$vals['telegram_chat_id'] : (getenv('TELEGRAM_CHAT_ID') ?: '');
    }

    /**
     * Can be called 2 ways:
     * 1) Internally: Api::send_booking_notification($dataArray)
     * 2) As HTTP endpoint: POST /api/telegram/send with fields:
     *    customer_name, address, therapist_name, package_name, date, time
     */
    public function send_booking_notification($data = null)
    {
        // If called from HTTP, build from POST
        if ($data === null) {
            $data = [
                'customer_name'  => $this->input->post('customer_name', true),
                'address'        => $this->input->post('address', true),
                'therapist_name' => $this->input->post('therapist_name', true),
                'package_name'   => $this->input->post('package_name', true),
                'date'           => $this->input->post('date', true),
                'time'           => $this->input->post('time', true),
                // Optional phone number (for WhatsApp link)
                'phone'          => $this->input->post('phone', true),
            ];
        }

        // Fallback placeholders
        $customer = $data['customer_name']  ?? '-';
        $address  = $data['address']        ?? '-';
        $thera    = $data['therapist_name'] ?? '-';
        $package  = $data['package_name']   ?? '-';
        $date     = $data['date']           ?? '-';
        $time     = $data['time']           ?? '-';

        // Neat Markdown message + WhatsApp link + form URL
        $formUrl = site_url('booking/form');
        $phoneRaw = isset($data['phone']) ? (string)$data['phone'] : '';
        $phoneSan = $phoneRaw ? preg_replace('/\D+/', '', $phoneRaw) : '';
        $message = "*SPA BOOKING REQUEST*\n\n"
                 . "👤 *Nama*: {$customer}\n"
                 . "🏠 *Alamat*: {$address}\n"
                 . "💅 *Paket*: {$package}\n"
                 . "‍♀️ *Terapis*: {$thera}\n"
                 . "📅 *Tanggal*: {$date}\n"
                 . "⏰ *Jam*: {$time}\n"
                 . "📞 *Telefon*: " . ($phoneSan ? "[{$phoneRaw}](https://wa.me/{$phoneSan})" : "-") . "\n"
                 . "\n[📄 Buka Borang Tempahan]({$formUrl})";

        $ok = $this->telegram_send($message);
        // If this is an HTTP request, output JSON
        if ($this->input->method(true) !== null) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['ok' => (bool)$ok]));
        } else {
            return $ok;
        }
    }

    private function telegram_send($message)
    {
        if (empty($this->botToken) || empty($this->chatId)) {
            log_message('error', 'Telegram botToken/chatId not configured');
            return false;
        }

        $url = "https://api.telegram.org/bot{$this->botToken}/sendMessage";
        $post = [
            'chat_id'    => $this->chatId,
            'text'       => $message,
            'parse_mode' => 'Markdown'
        ];

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
            return false;
        }
        return true;
    }
}