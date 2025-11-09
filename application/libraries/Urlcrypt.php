<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Urlcrypt library
 * Reversible encryption for URL-safe IDs using AES-256-CBC with HMAC-SHA256.
 * Requires openssl extension. Key priority: $params['key'] > config['encryption_key'] > env APP_URL_KEY > fallback.
 */
class Urlcrypt
{
    /** @var CI_Controller */
    protected $CI;
    /** @var string binary 32 bytes key */
    protected $key;

    /**
     * @param array $params optional: ['key' => 'your-secret']
     */
    public function __construct($params = [])
    {
        $this->CI =& get_instance();

        $key = isset($params['key']) ? $params['key'] : null;
        if (!$key) {
            // Prefer CI global helper to avoid linter complaining about $this->CI->config
            $key = config_item('encryption_key');
        }
        if (!$key) {
            $key = getenv('APP_URL_KEY');
        }
        if (!$key) {
            $key = 'spa-urlcrypt-fallback-key';
            log_message('error', 'Urlcrypt: using fallback key. Set $config[\'encryption_key\'] or APP_URL_KEY for stronger security.');
        }
        // Normalize to 32-byte binary key
        $this->key = hash('sha256', (string)$key, true);

        if (!function_exists('openssl_encrypt')) {
            log_message('error', 'Urlcrypt: OpenSSL not available. Install/enable openssl extension.');
        }
    }

    /**
     * URL-safe base64 encode
     */
    protected function base64url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

   /**
     * URL-safe base64 decode
     */
    protected function base64url_decode($data)
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }
        return base64_decode(strtr($data, '-_', '+/'));
    }

    /**
     * Encode numeric ID to opaque, URL-safe token
     * @param int|string $id
     * @return string|null
     */
    public function encode($id)
    {
        $id = (string)(int)$id;
        if ($id === '0' && (int)$id !== 0) {
            return null;
        }
        try {
            $iv = random_bytes(16);
        } catch (Exception $e) {
            // Fallback if random_bytes unavailable (should not happen on PHP7+)
            $iv = openssl_random_pseudo_bytes(16);
        }
        $cipher = openssl_encrypt($id, 'AES-256-CBC', $this->key, OPENSSL_RAW_DATA, $iv);
        if ($cipher === false) {
            return null;
        }
        $mac = hash_hmac('sha256', $iv.$cipher, $this->key, true);
        return $this->base64url_encode($iv.$cipher.$mac);
    }

    /**
     * Decode token back to integer ID (returns null if invalid)
     * @param string $token
     * @return int|null
     */
    public function decode($token)
    {
        if (!is_string($token) || $token === '') {
            return null;
        }
        $bin = $this->base64url_decode($token);
        if ($bin === false || strlen($bin) < 16 + 16 + 32) {
            return null;
        }
        $iv     = substr($bin, 0, 16);
        $macRef = substr($bin, -32);
        $cipher = substr($bin, 16, -32);
        $macNow = hash_hmac('sha256', $iv.$cipher, $this->key, true);
        if (!hash_equals($macRef, $macNow)) {
            return null;
        }
        $plain = openssl_decrypt($cipher, 'AES-256-CBC', $this->key, OPENSSL_RAW_DATA, $iv);
        if ($plain === false || !ctype_digit($plain)) {
            return null;
        }
        return (int)$plain;
    }

    /**
     * Helper to build URL path segment from ID
     * @param int|string $id
     * @return string
     */
    public function encodePath($id)
    {
        $t = $this->encode($id);
        return $t ?: '';
    }
}