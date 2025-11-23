<?php
// Use modular layout header
$this->load->view('admin/layout/header', ['title' => isset($title) ? $title : 'Admin Dashboard']);

// Helpers
$fmtRupiah = function($n) {
    $n = is_numeric($n) ? (float)$n : 0.0;
    return 'RM ' . number_format($n, 0, ',', '.');
};
?>
<!-- Flash messages -->
<?php if (!empty($flash['success'])): ?>
  <div class="mb-4 rounded-md bg-green-900/50 p-4 text-sm text-green-300 border border-green-800">
    <?= htmlspecialchars($flash['success']); ?>
  </div>
<?php endif; ?>
<?php if (!empty($flash['error'])): ?>
  <div class="mb-4 rounded-md bg-red-900/50 p-4 text-sm text-red-300 border border-red-800">
    <?= htmlspecialchars($flash['error']); ?>
  </div>
<?php endif; ?>

<!-- Summary Metrics -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
  <div class="rounded-lg border border-gray-700 bg-gray-800 p-5 shadow-sm">
    <div class="text-xs uppercase tracking-wide text-sky-400 font-semibold">Today's Bookings</div>
    <div class="mt-2 text-4xl font-bold text-slate-100">
      <?= isset($summary['total_booking_today']) ? (int)$summary['total_booking_today'] : 0; ?>
    </div>
    <p class="mt-1 text-gray-400 text-sm">Number of bookings received today.</p>
  </div>
  <div class="rounded-lg border border-gray-700 bg-gray-800 p-5 shadow-sm">
    <div class="text-xs uppercase tracking-wide text-emerald-400 font-semibold">Today's Revenue</div>
    <div class="mt-2 text-4xl font-bold text-slate-100">
      <?= $fmtRupiah((float)($summary['total_pendapatan_today'] ?? 0)); ?>
    </div>
    <p class="mt-1 text-gray-400 text-sm">Collected from today's bookings.</p>
  </div>
  <div class="rounded-lg border border-gray-700 bg-gray-800 p-5 shadow-sm">
    <div class="text-xs uppercase tracking-wide text-indigo-400 font-semibold">Active Therapists</div>
    <div class="mt-2 text-4xl font-bold text-slate-100">
      <?= isset($summary['active_therapists']) ? (int)$summary['active_therapists'] : 0; ?>
    </div>
    <p class="mt-1 text-gray-400 text-sm">With "available" status.</p>
  </div>
</div>

<!-- Popular Packages -->
<div class="rounded-lg border border-gray-700 bg-gray-800 shadow-sm mb-6">
  <div class="px-5 py-3 border-b border-gray-700">
    <h2 class="text-base font-semibold text-slate-100">Popular Packages (Last 30 Days)</h2>
  </div>
  <div class="p-0">
    <div class="overflow-x-auto">
      <table class="min-w-full table-auto">
        <thead class="bg-gray-700">
          <tr class="text-left text-sm text-gray-300">
            <th class="px-5 py-3 w-12">#</th>
            <th class="px-5 py-3">Package Name</th>
            <th class="px-5 py-3 text-center">Total Bookings</th>
            <th class="px-5 py-3 text-right">Total Revenue</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-700">
        <?php if (!empty($popular)): ?>
          <?php $i = 1; foreach ($popular as $pkg): ?>
            <tr class="text-sm">
              <td class="px-5 py-3 text-gray-300"><?= $i++; ?></td>
              <td class="px-5 py-3 font-medium text-slate-100"><?= htmlspecialchars($pkg->name); ?></td>
              <td class="px-5 py-3 text-center text-gray-300"><?= (int)($pkg->total_booking ?? 0); ?></td>
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
            <td colspan="4" class="px-5 py-6 text-center text-gray-400">No popular package data available.</td>
          </tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Quick Actions -->
<div class="rounded-lg border border-gray-700 bg-gray-800 shadow-sm">
  <div class="px-5 py-3 border-b border-gray-700">
    <h2 class="text-base font-semibold text-slate-100">Quick Actions</h2>
  </div>
  <div class="p-5">
    <div class="flex flex-wrap gap-2">
      <a href="<?= site_url('admin/schedule'); ?>" class="inline-flex items-center gap-2 rounded-md bg-sky-600 text-white px-4 py-2 text-sm hover:bg-sky-700">
        View Schedule Calendar
      </a>
      <a href="<?= site_url('admin/report'); ?>" class="inline-flex items-center gap-2 rounded-md bg-emerald-600 text-white px-4 py-2 text-sm hover:bg-emerald-700">
        Open Revenue Report
      </a>
      <a href="<?= site_url('admin/therapists'); ?>" class="inline-flex items-center gap-2 rounded-md border border-gray-600 bg-gray-700 text-gray-200 px-4 py-2 text-sm hover:bg-gray-600">
        Manage Therapists
      </a>
      <a href="<?= site_url('admin/packages'); ?>" class="inline-flex items-center gap-2 rounded-md border border-gray-600 bg-gray-700 text-gray-200 px-4 py-2 text-sm hover:bg-gray-600">
        Manage Spa Packages
      </a>
      <a href="<?= site_url('admin/exclusive_treatments'); ?>" class="inline-flex items-center gap-2 rounded-md border border-gray-600 bg-gray-700 text-gray-200 px-4 py-2 text-sm hover:bg-gray-600">
        Manage Exclusive Treatments
      </a>
    </div>
  </div>
</div>

<?php
// Footer (modular)
$this->load->view('admin/layout/footer');