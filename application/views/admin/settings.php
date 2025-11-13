<?php
// Use the admin layout header
$this->load->view('admin/layout/header', ['title' => isset($title) ? $title : 'Pengaturan Sistem']);

// Prepare values
$bot  = isset($settings['telegram_bot_token']) ? (string)$settings['telegram_bot_token'] : '';
$chat = isset($settings['telegram_chat_id']) ? (string)$settings['telegram_chat_id'] : '';
$slider_interval = isset($settings['ad_slider_interval']) ? (int)$settings['ad_slider_interval'] : 2;
?>

<!-- Flash messages -->
<?php if (!empty($flash['success'])): ?>
  <div class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-700 border border-green-200">
    <?= htmlspecialchars($flash['success']); ?>
  </div>
<?php endif; ?>
<?php if (!empty($flash['error'])): ?>
  <div class="mb-4 rounded-md bg-red-50 p-4 text-sm text-red-700 border border-red-200">
    <?= $flash['error']; // validation_errors() already formatted ?>
  </div>
<?php endif; ?>

<div class="space-y-6">
  <!-- Telegram Bot Settings -->
  <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
    <div class="px-5 py-3 border-b border-gray-200">
      <h2 class="text-base font-semibold text-gray-900">Pengaturan Telegram Bot</h2>
      <p class="text-sm text-gray-600 mt-1">Masukkan token bot dan chat ID untuk menerima notifikasi pesanan melalui Telegram.</p>
    </div>

    <div class="p-5">
      <form action="<?= site_url('admin/settings'); ?>" method="post" class="space-y-5">
      <div>
        <label for="telegram_bot_token" class="block text-sm font-medium text-gray-700">Telegram Bot Token</label>
        <input
          type="text"
          id="telegram_bot_token"
          name="telegram_bot_token"
          value="<?= htmlspecialchars($bot); ?>"
          class="mt-1 w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500"
          placeholder="mis. 1234567890:AA...-...."
          required
          minlength="20"
        />
        <p class="mt-1 text-xs text-gray-500">Dapatkan token dari @BotFather di Telegram.</p>
      </div>

      <div>
        <label for="telegram_chat_id" class="block text-sm font-medium text-gray-700">Telegram Chat ID</label>
        <input
          type="text"
          id="telegram_chat_id"
          name="telegram_chat_id"
          value="<?= htmlspecialchars($chat); ?>"
          class="mt-1 w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500"
          placeholder="mis. -1001234567890 (channel/supergroup) atau 123456789 (user)"
          required
          minlength="3"
        />
        <p class="mt-1 text-xs text-gray-500">Gunakan ID user, grup, atau channel tempat bot menjadi anggota/admin.</p>
      </div>

      <div>
        <label for="ad_slider_interval" class="block text-sm font-medium text-gray-700">Interval Slider Iklan (detik)</label>
        <input
          type="number"
          id="ad_slider_interval"
          name="ad_slider_interval"
          value="<?= htmlspecialchars($slider_interval); ?>"
          class="mt-1 w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-500"
          placeholder="2"
          required
          min="1"
          max="60"
        />
        <p class="mt-1 text-xs text-gray-500">Interval waktu perpindahan gambar iklan dalam detik (1-60 detik).</p>
      </div>

      <div class="pt-2">
        <button type="submit" class="inline-flex items-center rounded-md bg-sky-600 text-white px-4 py-2 text-sm font-semibold hover:bg-sky-700">
          Simpan Pengaturan
        </button>
      </div>
      </form>
    </div>
  </div>
</div>

<?php
// Footer
$this->load->view('admin/layout/footer');
?>