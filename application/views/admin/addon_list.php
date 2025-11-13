<?php
$this->load->view('admin/layout/header', ['title' => isset($title) ? $title : 'Data Add-on']);
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

<div class="mb-4 flex justify-end">
  <button type="button" onclick="window.aoCreateOpen()" class="inline-flex items-center rounded-md bg-sky-600 text-white px-4 py-2 text-sm hover:bg-sky-700">Tambah Add-on</button>
</div>

<div class="rounded-lg border border-gray-200 bg-white shadow-sm">
  <div class="px-5 py-3 border-b border-gray-200">
    <h2 class="text-base font-semibold text-gray-900">Senarai Add-on</h2>
  </div>
  <div class="p-0 overflow-x-auto">
    <table class="min-w-full table-auto">
      <thead class="bg-gray-50">
        <tr class="text-left text-sm text-gray-600">
          <th class="px-5 py-3 w-12">#</th>
          <th class="px-5 py-3 w-48">Kategori</th>
          <th class="px-5 py-3">Nama</th>
          <th class="px-5 py-3 w-[420px]">Keterangan</th>
          <th class="px-5 py-3 text-right w-40">Harga</th>
          <th class="px-5 py-3 text-center w-28">Mata Wang</th>
          <th class="px-5 py-3 text-center w-24">Aktif</th>
          <th class="px-5 py-3 text-right w-56">Tindakan</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200">
      <?php if (!empty($addons)): ?>
        <?php $i = 1; foreach ($addons as $a): ?>
          <tr class="text-sm align-top">
            <td class="px-5 py-3"><?= $i++; ?></td>
            <td class="px-5 py-3"><div class="text-gray-700"><?= htmlspecialchars($a->category ?? '-'); ?></div></td>
            <td class="px-5 py-3"><div class="font-medium text-gray-900"><?= htmlspecialchars($a->name ?? '-'); ?></div></td>
            <td class="px-5 py-3"><div class="text-gray-700 truncate max-w-[400px]" title="<?= htmlspecialchars($a->description ?? ''); ?>"><?= htmlspecialchars($a->description ?? '-'); ?></div></td>
            <td class="px-5 py-3 text-right"><span class="font-semibold"><?= htmlspecialchars($a->currency ?? 'RM'); ?> <?= number_format((float)($a->price ?? 0), 0, ',', '.'); ?></span></td>
            <td class="px-5 py-3 text-center"><span class="inline-flex items-center px-2 py-1 rounded bg-gray-200 text-gray-700 text-xs"><?= htmlspecialchars($a->currency ?? 'RM'); ?></span></td>
            <td class="px-5 py-3 text-center">
              <?php $active = (int)($a->is_active ?? 0) === 1; ?>
              <span class="inline-flex items-center px-2 py-1 rounded text-white text-xs <?= $active ? 'bg-emerald-600' : 'bg-gray-400'; ?>"><?= $active ? 'Ya' : 'Tidak'; ?></span>
            </td>
            <td class="px-5 py-3 text-right">
              <a class="inline-flex items-center rounded-md border border-gray-300 bg-white text-gray-700 px-3 py-1.5 text-xs hover:bg-gray-50" href="<?= site_url('admin/addon/edit/' . (isset($a->token) ? $a->token : (int)$a->id)); ?>">Sunting</a>
              <a class="inline-flex items-center rounded-md border border-red-300 bg-white text-red-600 px-3 py-1.5 text-xs hover:bg-red-50" href="<?= site_url('admin/addon/delete/' . (isset($a->token) ? $a->token : (int)$a->id)); ?>" onclick="return confirm('Padam add-on ini?');">Padam</a>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="8" class="px-5 py-6 text-center text-gray-500">Belum ada data add-on.</td>
        </tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<footer class="pt-6 text-gray-500 text-xs">
  <p>© <?= date('Y'); ?> Sistem Pengurusan Spa</p>
</footer>

<?php
  $hasEdit   = isset($editItem) && isset($editItemId);
  $editToken = null;
  if ($hasEdit && isset($addons) && is_array($addons)) {
    foreach ($addons as $addonCandidate) {
      if ((int)($addonCandidate->id ?? 0) === (int)$editItemId) {
        $editToken = isset($addonCandidate->token) ? $addonCandidate->token : (string)$addonCandidate->id;
        break;
      }
    }
  }
?>

<!-- Edit Add-on Modal -->
<div id="aoEditModalOverlay" class="fixed inset-0 bg-black/40 <?= $hasEdit ? '' : 'hidden'; ?>"></div>
<div id="aoEditModal"
     role="dialog"
     aria-modal="true"
     aria-hidden="<?= $hasEdit ? 'false' : 'true'; ?>"
     class="fixed inset-0 z-50 <?= $hasEdit ? 'flex' : 'hidden'; ?> items-center justify-center p-4">
  <div class="w-full max-w-2xl bg-white rounded-xl shadow-xl ring-1 ring-gray-200">
    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
      <h3 class="text-lg font-semibold text-gray-900">Sunting Add-on</h3>
      <button type="button" class="text-gray-400 hover:text-gray-600" onclick="window.aoEditClose()" aria-label="Tutup">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <div class="p-6">
      <?php if ($hasEdit && isset($editItem)): ?>
        <form method="post" action="<?= site_url('admin/addon/edit/' . $editToken); ?>" class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label for="edit_category" class="block text-sm font-medium text-gray-700">Kategori</label>
            <input id="edit_category" name="category" type="text" value="<?= htmlspecialchars($editItem->category ?? ''); ?>" required minlength="2"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
          </div>

          <div>
            <label for="edit_name" class="block text-sm font-medium text-gray-700">Nama Add-on</label>
            <input id="edit_name" name="name" type="text" value="<?= htmlspecialchars($editItem->name ?? ''); ?>" required minlength="2"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
          </div>

          <div>
            <label for="edit_price" class="block text-sm font-medium text-gray-700">Harga</label>
            <input id="edit_price" name="price" type="number" min="0" step="1" required value="<?= (float)($editItem->price ?? 0); ?>"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-right">
          </div>

          <div>
            <label for="edit_currency" class="block text-sm font-medium text-gray-700">Mata Wang</label>
            <input id="edit_currency" name="currency" type="text" maxlength="10" required value="<?= htmlspecialchars($editItem->currency ?? 'RM'); ?>"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
          </div>

          <div class="md:col-span-2">
            <label for="edit_description" class="block text-sm font-medium text-gray-700">Keterangan</label>
            <textarea id="edit_description" name="description" rows="2"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500"><?= htmlspecialchars($editItem->description ?? ''); ?></textarea>
          </div>

          <div class="md:col-span-2">
            <label class="inline-flex items-center gap-2 text-sm text-gray-700">
              <input type="checkbox" name="is_active" value="1" <?= ((int)($editItem->is_active ?? 0) === 1) ? 'checked' : ''; ?> class="rounded border-gray-300 text-sky-600 focus:ring-sky-500">
              Aktif
            </label>
          </div>

          <div class="md:col-span-2 flex justify-end gap-3 mt-2">
            <button type="submit" class="inline-flex items-center rounded-md bg-sky-600 text-white px-4 py-2 text-sm hover:bg-sky-700">Simpan</button>
            <a href="<?= site_url('admin/addons'); ?>" class="inline-flex items-center rounded-md border border-gray-300 bg-white text-gray-700 px-4 py-2 text-sm hover:bg-gray-50">Batal</a>
          </div>
        </form>
      <?php else: ?>
        <p class="text-sm text-gray-500">Data add-on tidak ditemui.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Create Add-on Modal -->
<div id="aoCreateModalOverlay" class="fixed inset-0 bg-black/40 hidden"></div>
<div id="aoCreateModal"
     role="dialog"
     aria-modal="true"
     aria-hidden="true"
     class="fixed inset-0 z-50 hidden items-center justify-center p-4">
  <div class="w-full max-w-2xl bg-white rounded-xl shadow-xl ring-1 ring-gray-200">
    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
      <h3 class="text-lg font-semibold text-gray-900">Tambah Add-on</h3>
      <button type="button" class="text-gray-400 hover:text-gray-600" onclick="window.aoCreateClose()" aria-label="Tutup">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <div class="p-6">
      <form method="post" action="<?= site_url('admin/addon/create'); ?>" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label for="create_category" class="block text-sm font-medium text-gray-700">Kategori</label>
          <input id="create_category" name="category" type="text" required minlength="2" placeholder="Contoh: SHAVING / WAXING / FACIAL"
                 class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
        </div>

        <div>
          <label for="create_name" class="block text-sm font-medium text-gray-700">Nama Add-on</label>
          <input id="create_name" name="name" type="text" required minlength="2" placeholder="Contoh: Armpit"
                 class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
        </div>

        <div>
          <label for="create_price" class="block text-sm font-medium text-gray-700">Harga</label>
          <input id="create_price" name="price" type="number" min="0" step="1" required placeholder="Contoh: 30"
                 class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-right">
        </div>

        <div>
          <label for="create_currency" class="block text-sm font-medium text-gray-700">Mata Wang</label>
          <input id="create_currency" name="currency" type="text" maxlength="10" value="RM" placeholder="Contoh: RM"
                 class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
        </div>

        <div class="md:col-span-2">
          <label for="create_description" class="block text-sm font-medium text-gray-700">Keterangan</label>
          <textarea id="create_description" name="description" rows="2" placeholder="Opsional"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500"></textarea>
        </div>

        <div class="md:col-span-2">
          <label class="inline-flex items-center gap-2 text-sm text-gray-700">
            <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-sky-600 focus:ring-sky-500">
            Aktif
          </label>
        </div>

        <div class="md:col-span-2 flex justify-end gap-3 mt-2">
          <button type="submit" class="inline-flex items-center rounded-md bg-sky-600 text-white px-4 py-2 text-sm hover:bg-sky-700">Tambah</button>
          <button type="button" class="inline-flex items-center rounded-md border border-gray-300 bg-white text-gray-700 px-4 py-2 text-sm hover:bg-gray-50" onclick="window.aoCreateClose()">Batal</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
// Modal helpers
window.aoEditOpen = function() {
  var m = document.getElementById('aoEditModal');
  var o = document.getElementById('aoEditModalOverlay');
  if (m && o) { m.classList.remove('hidden'); m.classList.add('flex'); o.classList.remove('hidden'); m.setAttribute('aria-hidden', 'false'); }
};
window.aoEditClose = function() {
  var m = document.getElementById('aoEditModal');
  var o = document.getElementById('aoEditModalOverlay');
  if (m && o) { m.classList.add('hidden'); m.classList.remove('flex'); o.classList.add('hidden'); m.setAttribute('aria-hidden', 'true'); }
};
window.aoCreateOpen = function() {
  var m = document.getElementById('aoCreateModal');
  var o = document.getElementById('aoCreateModalOverlay');
  if (m && o) { m.classList.remove('hidden'); m.classList.add('flex'); o.classList.remove('hidden'); m.setAttribute('aria-hidden', 'false'); }
};
window.aoCreateClose = function() {
  var m = document.getElementById('aoCreateModal');
  var o = document.getElementById('aoCreateModalOverlay');
  if (m && o) { m.classList.add('hidden'); m.classList.remove('flex'); o.classList.add('hidden'); m.setAttribute('aria-hidden', 'true'); }
};

// ESC & overlay click to close
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    if (typeof window.aoEditClose === 'function') window.aoEditClose();
    if (typeof window.aoCreateClose === 'function') window.aoCreateClose();
  }
});
document.addEventListener('click', function(e){
  if (e.target && e.target.id === 'aoEditModalOverlay') {
    if (typeof window.aoEditClose === 'function') window.aoEditClose();
  }
  if (e.target && e.target.id === 'aoCreateModalOverlay') {
    if (typeof window.aoCreateClose === 'function') window.aoCreateClose();
  }
});
</script>

<?php $this->load->view('admin/layout/footer'); ?>