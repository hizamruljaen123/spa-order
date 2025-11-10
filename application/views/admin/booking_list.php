<?php
$this->load->view('admin/layout/header', ['title' => isset($title) ? $title : 'Daftar Booking']);
?>

<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
  <div>
    <h2 class="text-base font-semibold text-gray-900">Daftar Booking</h2>
    <p class="text-sm text-gray-600">Lihat, tapis, dan ubah status pesanan.</p>
  </div>
  <div class="text-sm">
    <a href="<?= site_url('admin/schedule'); ?>" class="inline-flex items-center rounded-md border border-gray-300 bg-white text-gray-700 px-3 py-1.5 hover:bg-gray-50">Kalendar Jadwal</a>
  </div>
</div>

<?php if (!empty($flash['success'])): ?>
  <div class="mb-3 rounded-md bg-emerald-50 border border-emerald-200 text-emerald-900 px-4 py-2 text-sm"><?= $flash['success']; ?></div>
<?php endif; ?>
<?php if (!empty($flash['error'])): ?>
  <div class="mb-3 rounded-md bg-red-50 border border-red-200 text-red-900 px-4 py-2 text-sm"><?= $flash['error']; ?></div>
<?php endif; ?>

<!-- Filter bar -->
<form method="get" action="<?= site_url('admin/bookings'); ?>" class="rounded-lg border border-gray-200 bg-white p-3 shadow-sm">
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
    <div>
      <label class="block text-xs text-gray-600 mb-1">Dari Tanggal</label>
      <input type="date" name="from" value="<?= htmlspecialchars($filters['from'] ?? ''); ?>" class="w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm">
    </div>
    <div>
      <label class="block text-xs text-gray-600 mb-1">Sampai Tanggal</label>
      <input type="date" name="to" value="<?= htmlspecialchars($filters['to'] ?? ''); ?>" class="w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm">
    </div>
    <div>
      <label class="block text-xs text-gray-600 mb-1">Status</label>
      <?php $st = $filters['status'] ?? ''; ?>
      <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm">
        <option value="">(Semua)</option>
        <option value="pending"   <?= $st==='pending'?'selected':''; ?>>pending</option>
        <option value="accepted"  <?= $st==='accepted'?'selected':''; ?>>accepted</option>
        <option value="working"   <?= $st==='working'?'selected':''; ?>>working</option>
        <option value="rejected"  <?= $st==='rejected'?'selected':''; ?>>rejected</option>
        <option value="completed" <?= $st==='completed'?'selected':''; ?>>completed</option>
        <option value="canceled"  <?= $st==='canceled'?'selected':''; ?>>canceled</option>
      </select>
    </div>
    <div>
      <label class="block text-xs text-gray-600 mb-1">Terapis</label>
      <?php $tf = $filters['therapist_id'] ?? ''; ?>
      <select name="therapist_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm">
        <option value="">(Semua)</option>
        <?php if (!empty($therapists)): foreach ($therapists as $t): ?>
          <option value="<?= (int)$t->id; ?>" <?= ((string)$tf === (string)$t->id)?'selected':''; ?>><?= htmlspecialchars($t->name); ?></option>
        <?php endforeach; endif; ?>
      </select>
    </div>
    <div>
      <label class="block text-xs text-gray-600 mb-1">Paket</label>
      <?php $pf = $filters['package_id'] ?? ''; ?>
      <select name="package_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm">
        <option value="">(Semua)</option>
        <?php if (!empty($packages)): foreach ($packages as $p): ?>
          <option value="<?= (int)$p->id; ?>" <?= ((string)$pf === (string)$p->id)?'selected':''; ?>><?= htmlspecialchars($p->name); ?></option>
        <?php endforeach; endif; ?>
      </select>
    </div>
  </div>
  <div class="mt-3 flex items-center justify-end gap-2">
    <a href="<?= site_url('admin/bookings'); ?>" class="inline-flex items-center rounded-md border border-gray-300 bg-white text-gray-700 px-3 py-1.5 text-sm hover:bg-gray-50">Reset</a>
    <button type="submit" class="inline-flex items-center rounded-md bg-sky-600 text-white px-3 py-1.5 text-sm hover:bg-sky-700">Terapkan</button>
  </div>
</form>

<!-- Table -->
<div class="mt-4 rounded-lg border border-gray-200 bg-white p-0 shadow-sm overflow-x-auto">
  <table class="min-w-full divide-y divide-gray-200 text-sm">
    <thead class="bg-gray-50">
      <tr>
        <th class="px-4 py-2 text-left font-semibold text-gray-700">Pelanggan</th>
        <th class="px-4 py-2 text-left font-semibold text-gray-700">Paket</th>
        <th class="px-4 py-2 text-left font-semibold text-gray-700">Terapis</th>
        <th class="px-4 py-2 text-left font-semibold text-gray-700">Tanggal</th>
        <th class="px-4 py-2 text-left font-semibold text-gray-700">Jam</th>
        <th class="px-4 py-2 text-left font-semibold text-gray-700">Tipe</th>
        <th class="px-4 py-2 text-left font-semibold text-gray-700">Status</th>
        <th class="px-4 py-2 text-right font-semibold text-gray-700">Total</th>
        <th class="px-4 py-2 text-right font-semibold text-gray-700">Tindakan</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-200">
      <?php if (!empty($bookings) && is_array($bookings)): ?>
        <?php foreach ($bookings as $b): ?>
          <?php
            $s = strtolower((string)($b->status ?? ''));
            $badge = 'bg-gray-200 text-gray-800 border border-gray-300';
            switch ($s) {
              case 'pending':   $badge = 'bg-red-100 text-red-700 border border-red-300'; break;
              case 'accepted':
              case 'confirmed': $badge = 'bg-blue-100 text-blue-700 border border-blue-300'; break;
              case 'working':   $badge = 'bg-amber-100 text-amber-800 border border-amber-300'; break;
              case 'completed': $badge = 'bg-gray-300 text-gray-800 border border-gray-500'; break;
              case 'rejected':  $badge = 'bg-rose-100 text-rose-700 border border-rose-300'; break;
              case 'canceled':  $badge = 'bg-orange-100 text-orange-800 border border-orange-300'; break;
            }
            $idToken = isset($b->token) ? $b->token : (string)$b->id;
          ?>
          <tr data-token="<?= htmlspecialchars($idToken); ?>">
            <td class="px-4 py-2"><?= htmlspecialchars($b->customer_name ?? '-'); ?></td>
            <td class="px-4 py-2"><?= htmlspecialchars($b->package_name ?? '-'); ?></td>
            <td class="px-4 py-2"><?= htmlspecialchars($b->therapist_name ?? '-'); ?></td>
            <td class="px-4 py-2"><?= htmlspecialchars($b->date ?? '-'); ?></td>
            <td class="px-4 py-2"><?= htmlspecialchars(substr((string)($b->time ?? ''), 0, 5)); ?></td>
            <td class="px-4 py-2"><?= htmlspecialchars($b->call_type ?? '-'); ?></td>
            <td class="px-4 py-2">
              <span class="inline-flex items-center px-2 py-1 rounded text-xs <?= $badge; ?>"><?= htmlspecialchars($b->status ?? '-'); ?></span>
            </td>
            <td class="px-4 py-2 text-right">
              <?php
                $curr = isset($b->currency) ? $b->currency : 'RM';
                $total = isset($b->total_price) ? (float)$b->total_price : 0.0;
              ?>
              <span class="font-semibold"><?= htmlspecialchars($curr); ?> <?= number_format($total, 0, ',', '.'); ?></span>
            </td>
            <td class="px-4 py-2 text-right">
              <div class="inline-flex items-center gap-1">
                <button type="button" data-action="accepted"  class="px-2 py-1 rounded bg-blue-600 text-white text-xs hover:bg-blue-700">Terima</button>
                <button type="button" data-action="rejected"  class="px-2 py-1 rounded bg-rose-600 text-white text-xs hover:bg-rose-700">Tolak</button>
                <button type="button" data-action="working"   class="px-2 py-1 rounded bg-amber-500 text-white text-xs hover:bg-amber-600">On Working</button>
                <button type="button" data-action="completed" class="px-2 py-1 rounded bg-gray-700 text-white text-xs hover:bg-gray-800">Selesai</button>
                <button type="button" data-action="canceled"  class="px-2 py-1 rounded bg-orange-600 text-white text-xs hover:bg-orange-700">Batal</button>
                <a href="<?= site_url('admin/invoice'); ?>/<?= rawurlencode($idToken); ?>" target="_blank" rel="noopener" class="px-2 py-1 rounded border border-gray-300 text-gray-700 text-xs hover:bg-gray-50">Invoice</a>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td class="px-4 py-6 text-center text-gray-600" colspan="9">Belum ada data booking.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<script>
(function() {
  // Helper: POST x-www-form-urlencoded
  function apiPost(url, params) {
    const body = new URLSearchParams(params || {});
    return fetch(url, {
      method: 'POST',
      headers: { 'Accept': 'application/json', 'Content-Type': 'application/x-www-form-urlencoded' },
      credentials: 'same-origin',
      body
    }).then(resp => {
      if (!resp.ok) throw new Error('HTTP ' + resp.status);
      return resp.json().catch(() => ({}));
    });
  }

  function setStatus(token, status) {
    apiPost('<?= site_url('admin/booking/set-status'); ?>', { token, status })
      .then(res => {
        if (!res || !res.ok) throw new Error(res && res.error ? res.error : 'Gagal mengubah status.');
        // Refresh page to reflect updates
        window.location.reload();
      })
      .catch(err => alert('Gagal mengubah status: ' + err.message));
  }

  document.querySelectorAll('tr[data-token] .inline-flex [data-action]').forEach(function(btn) {
    btn.addEventListener('click', function() {
      const tr = btn.closest('tr[data-token]');
      if (!tr) return;
      const token = tr.getAttribute('data-token');
      const action = btn.getAttribute('data-action');
      if (!token || !action) return;
      setStatus(token, action);
    });
  });
})();
</script>

<?php $this->load->view('admin/layout/footer'); ?>