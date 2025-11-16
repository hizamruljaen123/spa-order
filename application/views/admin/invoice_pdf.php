<?php
$this->load->view('admin/layout/header', ['title' => isset($title) ? $title : 'Invoice']);

$booking   = isset($booking) ? $booking : null;
$invoice   = isset($invoice) ? $invoice : null;
$safe      = function($v, $fallback = '-') { return isset($v) && $v !== '' ? htmlspecialchars((string)$v) : $fallback; };
$currency  = ($booking && isset($booking->currency) && $booking->currency) ? $booking->currency : 'RM';
$fmt       = function($n) use ($currency) {
  $n = is_numeric($n) ? (float)$n : 0.0;
  return $currency . ' ' . number_format($n, 0, ',', '.');
};
$invNum    = $invoice ? $invoice->invoice_number : ('INV-' . date('Ymd') . '-XXXX');
$payStatus = $invoice ? $invoice->payment_status : 'DP';

// price + total
$price = 0.0;
if ($booking && isset($booking->package_price) && is_numeric($booking->package_price)) {
  $price = (float)$booking->package_price;
}
if (!$price && $booking && isset($booking->total_price) && is_numeric($booking->total_price)) {
  $price = (float)$booking->total_price;
}
$total = 0.0;
if ($invoice && isset($invoice->total) && is_numeric($invoice->total)) {
  $total = (float)$invoice->total;
} elseif ($booking && isset($booking->total_price) && is_numeric($booking->total_price)) {
  $total = (float)$booking->total_price;
} else {
  $total = $price;
}
?>

<!-- Invoice container -->
<div class="max-w-4xl mx-auto">
  <!-- Header -->
  <div class="rounded-lg border border-gray-200 bg-white shadow-sm mb-6">
    <div class="px-5 py-4 flex items-start justify-between">
      <div>
        <h2 class="text-xl font-semibold text-sky-600">SPA Management</h2>
        <p class="text-xs text-gray-500 mt-1">Jl. Example No. 123, Bandung • +62-812-3456-7890</p>
      </div>
      <div class="text-right">
        <div class="text-2xl font-bold text-gray-900 tracking-wide">INVOIS</div>
        <div class="mt-1 text-sm text-gray-700 font-medium"><?= $safe($invNum); ?></div>
        <div class="mt-2">
          <?php
            $badgeClass = 'bg-sky-600';
            if ($payStatus === 'Lunas') $badgeClass = 'bg-emerald-600';
            if ($payStatus === 'DP')    $badgeClass = 'bg-sky-600';
          ?>
          <span class="inline-flex items-center px-2 py-1 rounded text-white text-xs <?= $badgeClass; ?>"><?= $safe($payStatus); ?></span>
        </div>
      </div>
    </div>

    <div class="px-5 py-4 border-t border-gray-200 grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <div class="text-xs uppercase tracking-wide text-gray-500">Bill To</div>
        <div class="mt-2 text-sm text-gray-800">
          <div class="font-medium"><?= $safe($booking ? $booking->customer_name : null); ?></div>
          <div class="mt-1"><?= nl2br($safe($booking ? $booking->address : null)); ?></div>
        </div>
      </div>
      <div>
        <div class="text-xs uppercase tracking-wide text-gray-500">Booking Details</div>
        <div class="mt-2 text-sm text-gray-800">
          <div>Package: <span class="font-medium"><?= $safe($booking ? ($booking->package_name ?? null) : null); ?></span></div>
          <div>Date: <?= $safe($booking ? date('d M Y', strtotime($booking->date)) : null); ?></div>
          <div>Time: <?= $safe($booking ? substr($booking->time, 0, 5) : null); ?></div>
          <?php if ($booking && isset($booking->call_type)): ?>
            <div class="mt-1">Service Type: <?= $booking->call_type === 'OUT' ? 'Out Call' : 'In Call'; ?></div>
          <?php endif; ?>
          <?php if ($booking && isset($booking->therapist_name) && $booking->therapist_name): ?>
            <div class="mt-1">Therapist: <?= $safe($booking->therapist_name); ?></div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Items table -->
  <div class="rounded-lg border border-gray-200 bg-white shadow-sm mb-6 overflow-hidden">
    <div class="overflow-x-auto">
      <table class="min-w-full table-auto">
        <thead class="bg-gray-50">
          <tr class="text-left text-sm text-gray-600">
            <th class="px-5 py-3">Description</th>
            <th class="px-5 py-3 text-center w-28">Quantity</th>
            <th class="px-5 py-3 text-right w-40">Price</th>
            <th class="px-5 py-3 text-right w-40">Total</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 text-sm">
          <tr>
            <td class="px-5 py-3 align-top">
              <?= $safe($booking ? ($booking->package_name ?? 'Spa Package') : 'Spa Package'); ?>
              <div class="mt-1 text-xs text-gray-500">
                Therapist: <?= $safe($booking ? ($booking->therapist_name ?: '-') : '-'); ?>
              </div>
            </td>
            <td class="px-5 py-3 text-center align-top">1</td>
            <td class="px-5 py-3 text-right align-top"><?= $fmt($price); ?></td>
            <td class="px-5 py-3 text-right align-top"><?= $fmt($total); ?></td>
          </tr>
        </tbody>
        <tfoot class="bg-gray-50">
          <tr>
            <td class="px-5 py-3 text-right font-medium" colspan="3">Total</td>
            <td class="px-5 py-3 text-right font-bold"><?= $fmt($total); ?></td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>

  <!-- Notes -->
  <div class="rounded-lg border border-gray-200 bg-white shadow-sm mb-6">
    <div class="px-5 py-4">
      <div class="text-xs uppercase tracking-wide text-gray-500">Notes</div>
      <div class="mt-2 text-sm text-gray-700">
        <p>- Please include invoice number when confirming payment.</p>
        <p>- Unique number: <code class="text-sky-600"><?= $safe($invNum); ?></code></p>
        <p>- Thank you for using our services.</p>
      </div>
    </div>
  </div>

  <!-- Actions -->
  <div class="no-print flex flex-wrap gap-2">
    <button onclick="window.print()" class="inline-flex items-center rounded-md bg-sky-600 text-white px-4 py-2 text-sm hover:bg-sky-700">
      Print
    </button>
    <?php if ($booking): ?>
      <a href="<?= site_url('admin/invoice/generate/' . (isset($booking_token) ? $booking_token : (int)$booking->id)); ?>" class="inline-flex items-center rounded-md border border-gray-300 bg-white text-gray-700 px-4 py-2 text-sm hover:bg-gray-50">
        Download (HTML/Backup)
      </a>
    <?php endif; ?>
    <a href="<?= site_url('admin'); ?>" class="inline-flex items-center rounded-md border border-gray-300 bg-white text-gray-700 px-4 py-2 text-sm hover:bg-gray-50">
      Back to Dashboard
    </a>
  </div>

  <p class="no-print mt-2 text-xs text-gray-500">Use the "Print" button to save as PDF.</p>
</div>

<?php
$this->load->view('admin/layout/footer');
?>