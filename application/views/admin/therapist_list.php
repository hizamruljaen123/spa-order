<?php
$this->load->view('admin/layout/header', ['title' => isset($title) ? $title : 'Data Terapis']);
?>

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

<!-- Toolbar: Tambah Terapis (gunakan modal) -->
<div class="mb-4 flex justify-end">
  <button type="button" onclick="window.thrCreateOpen()" class="inline-flex items-center rounded-md bg-sky-600 text-white px-4 py-2 text-sm hover:bg-sky-700">Tambah Terapis</button>
</div>

<!-- List Therapist -->
<div class="rounded-lg border border-gray-200 bg-white shadow-sm">
  <div class="px-5 py-3 border-b border-gray-200">
    <h2 class="text-base font-semibold text-gray-900">Senarai Terapis</h2>
  </div>
  <div class="p-0">
    <div class="overflow-x-auto">
      <table class="min-w-full table-auto">
        <thead class="bg-gray-50">
          <tr class="text-left text-sm text-gray-600">
            <th class="px-5 py-3 w-12">#</th>
            <th class="px-5 py-3">Nama</th>
            <th class="px-5 py-3">No. Telefon</th>
            <th class="px-5 py-3 w-40">Status</th>
            <th class="px-5 py-3 w-56 text-right">Tindakan</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <?php if (!empty($therapists)): ?>
            <?php $i = 1; foreach ($therapists as $t): ?>
              <?php $isEdit = false; ?>
              <tr class="text-sm">
                <td class="px-5 py-3"><?= $i++; ?></td>
                <td class="px-5 py-3">
                  <?php if ($isEdit): ?>
                    <form method="post" action="<?= site_url('admin/therapist/edit/' . (isset($t->token) ? $t->token : (int)$t->id)); ?>" class="grid grid-cols-1 md:grid-cols-2 gap-2 items-center">
                      <input type="text" name="name" class="rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500" value="<?= htmlspecialchars($t->name); ?>" required minlength="2">
                      <input type="text" name="phone" class="rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500" value="<?= htmlspecialchars($t->phone ?? ''); ?>">
                  <?php else: ?>
                    <div class="font-medium text-gray-900"><?= htmlspecialchars($t->name); ?></div>
                  <?php endif; ?>
                </td>
                <td class="px-5 py-3">
                  <?php if (!$isEdit): ?>
                    <?= htmlspecialchars($t->phone ?? '-'); ?>
                  <?php endif; ?>
                </td>
                <td class="px-5 py-3">
                  <?php if ($isEdit): ?>
                    <select name="status" class="rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500" required>
                      <option value="available" <?= $t->status === 'available' ? 'selected' : ''; ?>>available</option>
                      <option value="busy" <?= $t->status === 'busy' ? 'selected' : ''; ?>>busy</option>
                      <option value="off" <?= $t->status === 'off' ? 'selected' : ''; ?>>off</option>
                    </select>
                  <?php else: ?>
                    <?php
                      $badgeClass = 'bg-gray-500';
                      if ($t->status === 'available') $badgeClass = 'bg-emerald-600';
                      elseif ($t->status === 'busy') $badgeClass = 'bg-amber-500';
                      elseif ($t->status === 'off') $badgeClass = 'bg-gray-400';
                    ?>
                    <span class="inline-flex items-center px-2 py-1 rounded text-white text-xs <?= $badgeClass; ?>"><?= htmlspecialchars($t->status); ?></span>
                  <?php endif; ?>
                </td>
                <td class="px-5 py-3 text-right">
                  <?php if ($isEdit): ?>
                    <button type="submit" class="inline-flex items-center rounded-md bg-sky-600 text-white px-3 py-1.5 text-xs hover:bg-sky-700">Simpan</button>
                    <a href="<?= site_url('admin/therapists'); ?>" class="inline-flex items-center rounded-md border border-gray-300 bg-white text-gray-700 px-3 py-1.5 text-xs hover:bg-gray-50">Batal</a>
                    </form>
                  <?php else: ?>
                    <a class="inline-flex items-center rounded-md border border-gray-300 bg-white text-gray-700 px-3 py-1.5 text-xs hover:bg-gray-50" href="<?= site_url('admin/therapist/edit/' . (isset($t->token) ? $t->token : (int)$t->id)); ?>">Sunting</a>
                    <a class="inline-flex items-center rounded-md border border-red-300 bg-white text-red-600 px-3 py-1.5 text-xs hover:bg-red-50" href="<?= site_url('admin/therapist/delete/' . (isset($t->token) ? $t->token : (int)$t->id)); ?>" onclick="return confirm('Padam terapis ini?');">Padam</a>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="5" class="px-5 py-6 text-center text-gray-500">Belum ada data terapis.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php
  // Modal state for edit therapist
  $hasEdit   = isset($editItem) && isset($editItemId);
  $editToken = null;
  if ($hasEdit && isset($therapists) && is_array($therapists)) {
    foreach ($therapists as $thrCandidate) {
      if ((int)($thrCandidate->id ?? 0) === (int)$editItemId) {
        $editToken = isset($thrCandidate->token) ? $thrCandidate->token : (string)$thrCandidate->id;
        break;
      }
    }
  }
?>

<!-- Edit Therapist Modal -->
<div id="thrEditModalOverlay" class="fixed inset-0 bg-black/40 <?= $hasEdit ? '' : 'hidden'; ?>"></div>
<div id="thrEditModal"
     role="dialog"
     aria-modal="true"
     aria-hidden="<?= $hasEdit ? 'false' : 'true'; ?>"
     class="fixed inset-0 z-50 <?= $hasEdit ? 'flex' : 'hidden'; ?> items-center justify-center p-4">
  <div class="w-full max-w-xl bg-white rounded-xl shadow-xl ring-1 ring-gray-200">
    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
      <h3 class="text-lg font-semibold text-gray-900">Sunting Terapis</h3>
      <button type="button" class="text-gray-400 hover:text-gray-600" onclick="window.thrEditClose()" aria-label="Tutup">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <div class="p-6">
      <?php if ($hasEdit && isset($editItem)): ?>
        <form method="post" action="<?= site_url('admin/therapist/edit/' . $editToken); ?>" class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="md:col-span-2">
            <label for="edit_name" class="block text-sm font-medium text-gray-700">Nama</label>
            <input id="edit_name" name="name" type="text" value="<?= htmlspecialchars($editItem->name ?? ''); ?>" required minlength="2"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
          </div>

          <div>
            <label for="edit_phone" class="block text-sm font-medium text-gray-700">No. Telefon</label>
            <input id="edit_phone" name="phone" type="text" value="<?= htmlspecialchars($editItem->phone ?? ''); ?>"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
          </div>

          <div>
            <label for="edit_status" class="block text-sm font-medium text-gray-700">Status</label>
            <select id="edit_status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500" required>
              <option value="available" <?= ($editItem->status ?? '') === 'available' ? 'selected' : ''; ?>>available</option>
              <option value="busy" <?= ($editItem->status ?? '') === 'busy' ? 'selected' : ''; ?>>busy</option>
              <option value="off" <?= ($editItem->status ?? '') === 'off' ? 'selected' : ''; ?>>off</option>
            </select>
          </div>

          <div class="md:col-span-2 flex justify-end gap-3 mt-2">
            <button type="submit" class="inline-flex items-center rounded-md bg-sky-600 text-white px-4 py-2 text-sm hover:bg-sky-700">Simpan</button>
            <a href="<?= site_url('admin/therapists'); ?>" class="inline-flex items-center rounded-md border border-gray-300 bg-white text-gray-700 px-4 py-2 text-sm hover:bg-gray-50">Batal</a>
          </div>
        </form>
      <?php else: ?>
        <p class="text-sm text-gray-500">Data terapis tidak ditemui.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Create Therapist Modal -->
<div id="thrCreateModalOverlay" class="fixed inset-0 bg-black/40 hidden"></div>
<div id="thrCreateModal"
     role="dialog"
     aria-modal="true"
     aria-hidden="true"
     class="fixed inset-0 z-50 hidden items-center justify-center p-4">
  <div class="w-full max-w-xl bg-white rounded-xl shadow-xl ring-1 ring-gray-200">
    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
      <h3 class="text-lg font-semibold text-gray-900">Tambah Terapis</h3>
      <button type="button" class="text-gray-400 hover:text-gray-600" onclick="window.thrCreateClose()" aria-label="Tutup">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <div class="p-6">
      <form method="post" action="<?= site_url('admin/therapist/create'); ?>" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="md:col-span-2">
          <label for="create_name" class="block text-sm font-medium text-gray-700">Nama</label>
          <input id="create_name" name="name" type="text" required minlength="2" placeholder="Nama terapis"
                 class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
        </div>

        <div>
          <label for="create_phone" class="block text-sm font-medium text-gray-700">No. Telefon</label>
          <input id="create_phone" name="phone" type="text" placeholder="01xxxxxxxx"
                 class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
        </div>

        <div>
          <label for="create_status" class="block text-sm font-medium text-gray-700">Status</label>
          <select id="create_status" name="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
            <option value="available">available</option>
            <option value="busy">busy</option>
            <option value="off">off</option>
          </select>
        </div>

        <div class="md:col-span-2 flex justify-end gap-3 mt-2">
          <button type="submit" class="inline-flex items-center rounded-md bg-sky-600 text-white px-4 py-2 text-sm hover:bg-sky-700">Tambah</button>
          <button type="button" class="inline-flex items-center rounded-md border border-gray-300 bg-white text-gray-700 px-4 py-2 text-sm hover:bg-gray-50" onclick="window.thrCreateClose()">Batal</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  // Modal helpers for Therapist
  window.thrEditOpen = function() {
    var m = document.getElementById('thrEditModal');
    var o = document.getElementById('thrEditModalOverlay');
    if (m && o) { m.classList.remove('hidden'); m.classList.add('flex'); o.classList.remove('hidden'); m.setAttribute('aria-hidden', 'false'); }
  };
  window.thrEditClose = function() {
    var m = document.getElementById('thrEditModal');
    var o = document.getElementById('thrEditModalOverlay');
    if (m && o) { m.classList.add('hidden'); m.classList.remove('flex'); o.classList.add('hidden'); m.setAttribute('aria-hidden', 'true'); }
  };
  window.thrCreateOpen = function() {
    var m = document.getElementById('thrCreateModal');
    var o = document.getElementById('thrCreateModalOverlay');
    if (m && o) { m.classList.remove('hidden'); m.classList.add('flex'); o.classList.remove('hidden'); m.setAttribute('aria-hidden', 'false'); }
  };
  window.thrCreateClose = function() {
    var m = document.getElementById('thrCreateModal');
    var o = document.getElementById('thrCreateModalOverlay');
    if (m && o) { m.classList.add('hidden'); m.classList.remove('flex'); o.classList.add('hidden'); m.setAttribute('aria-hidden', 'true'); }
  };

  // Close on ESC and overlay click (page-local handling)
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      window.thrEditClose();
      window.thrCreateClose();
    }
  });
  document.addEventListener('click', function(e) {
    if (e.target && (e.target.id === 'thrEditModalOverlay')) window.thrEditClose();
    if (e.target && (e.target.id === 'thrCreateModalOverlay')) window.thrCreateClose();
  });
</script>

<?php $this->load->view('admin/layout/footer'); ?>