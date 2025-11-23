<?php
$this->load->view('admin/layout/header', ['title' => isset($title) ? $title : 'Booking List']);
?>

<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
  <div>
    <h2 class="text-base font-semibold text-slate-100">Booking List</h2>
    <p class="text-sm text-gray-400">View, filter, and manage order status.</p>
  </div>
  <div class="text-sm">
    <a href="<?= site_url('admin/schedule'); ?>" class="inline-flex items-center rounded-md border border-gray-600 bg-gray-700 text-gray-200 px-3 py-1.5 hover:bg-gray-600">Schedule Calendar</a>
  </div>
</div>

<?php if (!empty($flash['success'])): ?>
  <div class="mb-3 rounded-md bg-emerald-900/50 border border-emerald-800 text-emerald-300 px-4 py-2 text-sm"><?= $flash['success']; ?></div>
<?php endif; ?>
<?php if (!empty($flash['error'])): ?>
  <div class="mb-3 rounded-md bg-red-900/50 border border-red-800 text-red-300 px-4 py-2 text-sm"><?= $flash['error']; ?></div>
<?php endif; ?>

<!-- Filter bar -->
<form method="get" action="<?= site_url('admin/bookings'); ?>" class="rounded-lg border border-gray-700 bg-gray-800 p-3 shadow-sm">
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
    <div>
      <label class="block text-xs text-gray-400 mb-1">From Date</label>
      <input type="date" name="from" value="<?= htmlspecialchars($filters['from'] ?? ''); ?>" class="w-full rounded-md border-gray-600 bg-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm text-slate-100">
    </div>
    <div>
      <label class="block text-xs text-gray-400 mb-1">To Date</label>
      <input type="date" name="to" value="<?= htmlspecialchars($filters['to'] ?? ''); ?>" class="w-full rounded-md border-gray-600 bg-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm text-slate-100">
    </div>
    <div>
      <label class="block text-xs text-gray-400 mb-1">Status</label>
      <?php $st = $filters['status'] ?? ''; ?>
      <select name="status" class="w-full rounded-md border-gray-600 bg-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm text-slate-100">
        <option value="">(All)</option>
        <option value="pending"   <?= $st==='pending'?'selected':''; ?>>pending</option>
        <option value="accepted"  <?= $st==='accepted'?'selected':''; ?>>accepted</option>
        <option value="working"   <?= $st==='working'?'selected':''; ?>>working</option>
        <option value="rejected"  <?= $st==='rejected'?'selected':''; ?>>rejected</option>
        <option value="completed" <?= $st==='completed'?'selected':''; ?>>completed</option>
        <option value="canceled"  <?= $st==='canceled'?'selected':''; ?>>canceled</option>
      </select>
    </div>
    <div>
      <label class="block text-xs text-gray-400 mb-1">Therapist</label>
      <?php $tf = $filters['therapist_id'] ?? ''; ?>
      <select name="therapist_id" class="w-full rounded-md border-gray-600 bg-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm text-slate-100">
        <option value="">(All)</option>
        <?php if (!empty($therapists)): foreach ($therapists as $t): ?>
          <option value="<?= (int)$t->id; ?>" <?= ((string)$tf === (string)$t->id)?'selected':''; ?>><?= htmlspecialchars($t->name); ?></option>
        <?php endforeach; endif; ?>
      </select>
    </div>
    <div>
      <label class="block text-xs text-gray-400 mb-1">Package</label>
      <?php $pf = $filters['package_id'] ?? ''; ?>
      <select name="package_id" class="w-full rounded-md border-gray-600 bg-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm text-slate-100">
        <option value="">(All)</option>
        <?php if (!empty($packages)): foreach ($packages as $p): ?>
          <option value="<?= (int)$p->id; ?>" <?= ((string)$pf === (string)$p->id)?'selected':''; ?>><?= htmlspecialchars($p->name); ?></option>
        <?php endforeach; endif; ?>
      </select>
    </div>
  </div>
  <div class="mt-3 flex items-center justify-end gap-2">
    <a href="<?= site_url('admin/bookings'); ?>" class="inline-flex items-center rounded-md border border-gray-600 bg-gray-700 text-gray-200 px-3 py-1.5 text-sm hover:bg-gray-600">Reset</a>
    <button type="submit" class="inline-flex items-center rounded-md bg-sky-600 text-white px-3 py-1.5 text-sm hover:bg-sky-700">Apply</button>
  </div>
</form>

<!-- Table -->
<div class="mt-4 rounded-lg border border-gray-700 bg-gray-800 p-0 shadow-sm overflow-x-auto">
  <table class="min-w-full divide-y divide-gray-700 text-sm">
    <thead class="bg-gray-700">
      <tr>
        <th class="px-4 py-2 text-left font-semibold text-gray-300">Customer</th>
        <th class="px-4 py-2 text-left font-semibold text-gray-300">Package</th>
        <th class="px-4 py-2 text-left font-semibold text-gray-300">Therapist</th>
        <th class="px-4 py-2 text-left font-semibold text-gray-300">Date</th>
        <th class="px-4 py-2 text-left font-semibold text-gray-300">Time</th>
        <th class="px-4 py-2 text-left font-semibold text-gray-300">Type</th>
        <th class="px-4 py-2 text-left font-semibold text-gray-300">Status</th>
        <th class="px-4 py-2 text-right font-semibold text-gray-300">Total</th>
        <th class="px-4 py-2 text-right font-semibold text-gray-300">Actions</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-700">
      <?php if (!empty($bookings) && is_array($bookings)): ?>
        <?php foreach ($bookings as $b): ?>
          <?php
            $s = strtolower((string)($b->status ?? ''));
            $badge = 'bg-gray-700 text-gray-200 border border-gray-600';
            switch ($s) {
              case 'pending':   $badge = 'bg-red-900/50 text-red-300 border border-red-800'; break;
              case 'accepted':
              case 'confirmed': $badge = 'bg-blue-900/50 text-blue-300 border border-blue-800'; break;
              case 'working':   $badge = 'bg-amber-900/50 text-amber-300 border border-amber-800'; break;
              case 'completed': $badge = 'bg-gray-600 text-gray-200 border border-gray-500'; break;
              case 'rejected':  $badge = 'bg-rose-900/50 text-rose-300 border border-rose-800'; break;
              case 'canceled':  $badge = 'bg-orange-900/50 text-orange-300 border border-orange-800'; break;
            }
            $idToken = isset($b->token) ? $b->token : (string)$b->id;
          ?>
          <tr data-token="<?= htmlspecialchars($idToken); ?>">
            <td class="px-4 py-2 text-gray-300"><?= htmlspecialchars($b->customer_name ?? '-'); ?></td>
            <td class="px-4 py-2 text-gray-300">
              <?php
                $package_display = $b->package_name ?? '-';
                if (!empty($b->package_is_deleted)) {
                  $package_display .= ' <span class="text-xs bg-red-900/50 text-red-300 px-1 rounded">(Deleted)</span>';
                }
                echo $package_display;
              ?>
            </td>
            <td class="px-4 py-2 text-gray-300"><?= htmlspecialchars($b->therapist_name ?? '-'); ?></td>
            <td class="px-4 py-2 text-gray-300"><?= htmlspecialchars($b->date ?? '-'); ?></td>
            <td class="px-4 py-2 text-gray-300"><?= htmlspecialchars(substr((string)($b->time ?? ''), 0, 5)); ?></td>
            <td class="px-4 py-2 text-gray-300"><?= htmlspecialchars($b->call_type ?? '-'); ?></td>
            <td class="px-4 py-2">
              <span class="inline-flex items-center px-2 py-1 rounded text-xs <?= $badge; ?>"><?= htmlspecialchars($b->status ?? '-'); ?></span>
            </td>
            <td class="px-4 py-2 text-right text-gray-300">
              <?php
                $curr = isset($b->currency) ? $b->currency : 'RM';
                $total = isset($b->total_price) ? (float)$b->total_price : 0.0;
              ?>
              <span class="font-semibold"><?= htmlspecialchars($curr); ?> <?= number_format($total, 0, ',', '.'); ?></span>
            </td>
            <td class="px-4 py-2 text-right">
              <div class="inline-flex items-center gap-1">
                <button type="button" data-action="accepted"  class="px-2 py-1 rounded bg-blue-600 text-white text-xs hover:bg-blue-700">Accept</button>
                <button type="button" data-action="rejected"  class="px-2 py-1 rounded bg-rose-600 text-white text-xs hover:bg-rose-700">Reject</button>
                <button type="button" data-action="working"   class="px-2 py-1 rounded bg-amber-500 text-white text-xs hover:bg-amber-600">Working</button>
                <button type="button" data-action="completed" class="px-2 py-1 rounded bg-gray-600 text-white text-xs hover:bg-gray-700">Complete</button>
                <button type="button" data-action="canceled"  class="px-2 py-1 rounded bg-orange-600 text-white text-xs hover:bg-orange-700">Cancel</button>
                <a href="<?= site_url('admin/invoice'); ?>/<?= rawurlencode($idToken); ?>" target="_blank" rel="noopener" class="px-2 py-1 rounded border border-gray-600 bg-gray-700 text-gray-200 text-xs hover:bg-gray-600">Invoice</a>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td class="px-4 py-6 text-center text-gray-400" colspan="9">No booking data available.</td>
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
        if (!res || !res.ok) throw new Error(res && res.error ? res.error : 'Failed to update status.');
        // Refresh page to reflect updates
        window.location.reload();
      })
      .catch(err => alert('Failed to update status: ' + err.message));
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