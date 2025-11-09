<?php
// Use modular layout header
$this->load->view('admin/layout/header', ['title' => isset($title) ? $title : 'Admin Dashboard']);

// Helpers
$fmtRupiah = function($n) {
    $n = is_numeric($n) ? (float)$n : 0.0;
    return 'Rp ' . number_format($n, 0, ',', '.');
};
?>
<!-- Flash messages -->
<?php if (!empty($flash['success'])): ?>
  <div class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-700 border border-green-200">
    <?= htmlspecialchars($flash['success']); ?>
  </div>
<?php endif; ?>
<?php if (!empty($flash['error'])): ?>
  <div class="mb-4 rounded-md bg-red-50 p-4 text-sm text-red-700 border border-red-200">
    <?= htmlspecialchars($flash['error']); ?>
  </div>
<?php endif; ?>

<!-- Summary Metrics -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
  <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
    <div class="text-xs uppercase tracking-wide text-sky-600 font-semibold">Total Booking Hari Ini</div>
    <div class="mt-2 text-4xl font-bold text-gray-900">
      <?= isset($summary['total_booking_today']) ? (int)$summary['total_booking_today'] : 0; ?>
    </div>
    <p class="mt-1 text-gray-500 text-sm">Jumlah pesanan yang masuk hari ini.</p>
  </div>
  <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
    <div class="text-xs uppercase tracking-wide text-emerald-600 font-semibold">Total Pendapatan Hari Ini</div>
    <div class="mt-2 text-4xl font-bold text-gray-900">
      <?= $fmtRupiah((float)($summary['total_pendapatan_today'] ?? 0)); ?>
    </div>
    <p class="mt-1 text-gray-500 text-sm">Akumulasi dari pesanan hari ini.</p>
  </div>
  <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
    <div class="text-xs uppercase tracking-wide text-indigo-600 font-semibold">Therapist Aktif</div>
    <div class="mt-2 text-4xl font-bold text-gray-900">
      <?= isset($summary['active_therapists']) ? (int)$summary['active_therapists'] : 0; ?>
    </div>
    <p class="mt-1 text-gray-500 text-sm">Dengan status "available".</p>
  </div>
</div>

<!-- Popular Packages -->
<div class="rounded-lg border border-gray-200 bg-white shadow-sm mb-6">
  <div class="px-5 py-3 border-b border-gray-200">
    <h2 class="text-base font-semibold text-gray-900">Paket Populer (30 Hari Terakhir)</h2>
  </div>
  <div class="p-0">
    <div class="overflow-x-auto">
      <table class="min-w-full table-auto">
        <thead class="bg-gray-50">
          <tr class="text-left text-sm text-gray-600">
            <th class="px-5 py-3 w-12">#</th>
            <th class="px-5 py-3">Nama Paket</th>
            <th class="px-5 py-3 text-center">Total Booking</th>
            <th class="px-5 py-3 text-right">Total Pendapatan</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
        <?php if (!empty($popular)): ?>
          <?php $i = 1; foreach ($popular as $pkg): ?>
            <tr class="text-sm">
              <td class="px-5 py-3"><?= $i++; ?></td>
              <td class="px-5 py-3 font-medium text-gray-900"><?= htmlspecialchars($pkg->name); ?></td>
              <td class="px-5 py-3 text-center"><?= (int)($pkg->total_booking ?? 0); ?></td>
              <td class="px-5 py-3 text-right">
                <?php
                  $total = isset($pkg->total_pendapatan) ? (float)$pkg->total_pendapatan : 0.0;
                  echo $fmtRupiah($total);
                ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="4" class="px-5 py-6 text-center text-gray-500">Belum ada data paket populer.</td>
          </tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Quick Actions -->
<div class="rounded-lg border border-gray-200 bg-white shadow-sm">
  <div class="px-5 py-3 border-b border-gray-200">
    <h2 class="text-base font-semibold text-gray-900">Aksi Cepat</h2>
  </div>
  <div class="p-5">
    <div class="flex flex-wrap gap-2">
      <a href="<?= site_url('admin/schedule'); ?>" class="inline-flex items-center gap-2 rounded-md bg-sky-600 text-white px-4 py-2 text-sm hover:bg-sky-700">
        Lihat Kalender Jadwal
      </a>
      <a href <?= site_url('admin/report'); ?> class="inline-flex items-center gap-2 rounded-md bg-emerald-600 text-white px-4 py-2 text-sm hover:bg-emerald-700">
        Buka Laporan Pendapatan
      </a>
      <a href="<?= site_url('admin/therapists'); ?>" class="inline-flex items-center gap-2 rounded-md border border-gray-300 bg-white text-gray-700 px-4 py-2 text-sm hover:bg-gray-50">
        Kelola Therapist
      </a>
      <a href="<?= site_url('admin/packages'); ?>" class="inline-flex items-center gap-2 rounded-md border border-gray-300 bg-white text-gray-700 px-4 py-2 text-sm hover:bg-gray-50">
        Kelola Paket Spa
      </a>
    </div>
  </div>
</div>

<?php
// Footer (modular)
$this->load->view('admin/layout/footer');