<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * @property CI_Input $input
 * @property CI_Output $output
 */

class Api extends CI_Controller
{
    // TODO: Replace with your real credentials (or move to config)
    private $botToken = 'YOUR_TELEGRAM_BOT_TOKEN';
    private $chatId   = 'YOUR_CHAT_ID';

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
        $this->load->helper('security');
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
            ];
        }

        // Fallback placeholders
        $customer = $data['customer_name']  ?? '-';
        $address  = $data['address']        ?? '-';
        $thera    = $data['therapist_name'] ?? '-';
        $package  = $data['package_name']   ?? '-';
        $date     = $data['date']           ?? '-';
        $time     = $data['time']           ?? '-';

        $message = "📋 *SPA BOOKING REQUEST*\n"
                 . "👤 Nama: {$customer}\n"
                 . "🏠 Alamat: {$address}\n"
                 . "💆‍♀️ Therapist: {$thera}\n"
                 . "💅 Paket: {$package}\n"
                 . "📅 Tanggal: {$date}\n"
                 . "⏰ Jam: {$time}";

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