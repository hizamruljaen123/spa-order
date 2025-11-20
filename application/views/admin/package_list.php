<?php
$this->load->view('admin/layout/header', ['title' => isset($title) ? $title : 'Spa Package Data (APITT Menu)']);
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

<!-- Toolbar: Add Package (modal) -->
<div class="mb-4 flex justify-end">
  <button type="button" onclick="window.pkgCreateOpen()" class="inline-flex items-center rounded-md bg-sky-600 text-white px-4 py-2 text-sm hover:bg-sky-700">Add Package</button>
</div>

<!-- List Packages -->
<div class="rounded-lg border border-gray-200 bg-white shadow-sm">
  <div class="px-5 py-3 border-b border-gray-200">
    <h2 class="text-base font-semibold text-gray-900">Package List (APITT)</h2>
  </div>
  <div class="p-0">
    <div class="overflow-x-auto">
      <table class="min-w-full table-auto">
        <thead class="bg-gray-50">
          <tr class="text-left text-sm text-gray-600">
            <th class="px-5 py-3 w-12">#</th>
            <th class="px-5 py-3">Package Name</th>
            <th class="px-5 py-3">Category</th>
            <th class="px-5 py-3 text-center w-32">Therapists</th>
            <th class="px-5 py-3 text-center w-36">Duration</th>
            <th class="px-5 py-3 text-right w-40">Price</th>
            <th class="px-5 py-3 text-center w-28">Currency</th>
            <th class="px-5 py-3 w-[360px]">Description</th>
            <th class="px-5 py-3 text-right w-56">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
        <?php if (!empty($packages)): ?>
          <?php $i = 1; foreach ($packages as $p): ?>
            <?php $isEdit = isset($editItemId) && (int)$editItemId === (int)$p->id; ?>
            <tr class="text-sm align-top">
              <td class="px-5 py-3"><?= $i++; ?></td>

              <!-- Name -->
              <td class="px-5 py-3">
                <?php if (false): ?>
                  <form method="post" action="<?= site_url('admin/package/edit/' . (isset($p->token) ? $p->token : (int)$p->id)); ?>" class="grid grid-cols-1 gap-2">
                    <input type="text" name="name" class="rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500" value="<?= htmlspecialchars($p->name); ?>" required minlength="2">
                <?php else: ?>
                  <div class="flex items-center gap-2">
                    <div class="font-medium text-gray-900"><?= htmlspecialchars($p->name); ?></div>
                    <?php if (!empty($p->is_deleted)): ?>
                      <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Sudah Dihapus</span>
                    <?php endif; ?>
                  </div>
                <?php endif; ?>
              </td>

              <!-- Category -->
              <td class="px-5 py-3">
                <?php if (false): ?>
                  <input type="text" name="category" class="rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500" value="<?= htmlspecialchars($p->category ?? ''); ?>" required>
                <?php else: ?>
                  <div class="text-gray-600"><?= htmlspecialchars($p->category ?? '-'); ?></div>
                <?php endif; ?>
              </td>

              <!-- Hands -->
              <td class="px-5 py-3 text-center">
                <?php if (false): ?>
                  <select name="hands" class="rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
                    <option value="1" <?= ((int)($p->hands ?? 1) === 1) ? 'selected' : ''; ?>>1</option>
                    <option value="2" <?= ((int)($p->hands ?? 1) === 2) ? 'selected' : ''; ?>>2</option>
                  </select>
                <?php else: ?>
                  <?= (int)($p->hands ?? 1); ?>
                <?php endif; ?>
              </td>

              <!-- Duration -->
              <td class="px-5 py-3 text-center">
                <?php if (false): ?>
                  <input type="number" name="duration" class="w-24 text-center rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500" value="<?= (int)($p->duration ?? 0); ?>" min="15" step="5" required>
                <?php else: ?>
                  <?= (int)($p->duration ?? 0); ?> minutes
                <?php endif; ?>
              </td>

              <!-- Price -->
              <td class="px-5 py-3 text-right">
                <?php if (false): ?>
                  <input type="number" name="price_in_call" class="w-28 text-right rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500" value="<?= (float)($p->price_in_call ?? 0); ?>" min="0" step="1" required>
                <?php else: ?>
                  <?= htmlspecialchars($p->currency ?? 'RM'); ?> <?= number_format((float)($p->price_in_call ?? 0), 0, ',', '.'); ?>
                <?php endif; ?>
              </td>

              <!-- Currency -->
              <td class="px-5 py-3 text-center">
                <?php if (false): ?>
                  <input type="text" name="currency" class="w-20 text-center rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500" value="<?= htmlspecialchars($p->currency ?? 'RM'); ?>" maxlength="10" required>
                <?php else: ?>
                  <span class="inline-flex items-center px-2 py-1 rounded bg-gray-200 text-gray-700 text-xs"><?= htmlspecialchars($p->currency ?? 'RM'); ?></span>
                <?php endif; ?>
              </td>

              <!-- Description -->
              <td class="px-5 py-3">
                <?php if (false): ?>
                  <textarea name="description" class="w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500" rows="1"><?= htmlspecialchars($p->description ?? ''); ?></textarea>
                <?php else: ?>
                  <div class="text-gray-700 truncate max-w-[340px]"><?= htmlspecialchars($p->description ?? '-'); ?></div>
                <?php endif; ?>
              </td>

              <!-- Actions -->
              <td class="px-5 py-3 text-right">
                <?php if (false): ?>
                    <!-- Inline edit disabled; using modal -->
                <?php else: ?>
                    <?php if (empty($p->is_deleted)): ?>
                      <a class="inline-flex items-center rounded-md border border-gray-300 bg-white text-gray-700 px-3 py-1.5 text-xs hover:bg-gray-50" href="<?= site_url('admin/package/edit/' . (isset($p->token) ? $p->token : (int)$p->id)); ?>">Edit</a>
                      <a class="inline-flex items-center rounded-md border border-red-300 bg-white text-red-600 px-3 py-1.5 text-xs hover:bg-red-50" href="<?= site_url('admin/package/delete/' . (isset($p->token) ? $p->token : (int)$p->id)); ?>" onclick="return confirm('Hapus paket ini? (Data booking historis akan tetap terjaga)');">Delete</a>
                    <?php else: ?>
                      <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">Tidak Aktif</span>
                      <a class="inline-flex items-center rounded-md border border-green-300 bg-white text-green-600 px-3 py-1.5 text-xs hover:bg-green-50" href="<?= site_url('admin/package/restore/' . (isset($p->token) ? $p->token : (int)$p->id)); ?>" onclick="return confirm('Pulihkan paket ini?');">Restore</a>
                    <?php endif; ?>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="9" class="px-5 py-6 text-center text-gray-500">No package data available.</td>
          </tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<footer class="pt-6 text-gray-500 text-xs">
  <p>© <?= date('Y'); ?> Spa Management System</p>
</footer>

<?php
  // Prepare modal data (token lookup for the edited item)
  $hasEdit   = isset($editItem) && isset($editItemId);
  $editToken = null;
  if ($hasEdit && isset($packages) && is_array($packages)) {
    foreach ($packages as $pkgCandidate) {
      if ((int)($pkgCandidate->id ?? 0) === (int)$editItemId) {
        $editToken = isset($pkgCandidate->token) ? $pkgCandidate->token : (string)$pkgCandidate->id;
        break;
      }
    }
  }
?>

<!-- Edit Package Modal -->
<div id="pkgEditModalOverlay" class="fixed inset-0 bg-black/40 <?= $hasEdit ? '' : 'hidden'; ?>"></div>
<div id="pkgEditModal"
     role="dialog"
     aria-modal="true"
     aria-hidden="<?= $hasEdit ? 'false' : 'true'; ?>"
     class="fixed inset-0 z-50 <?= $hasEdit ? 'flex' : 'hidden'; ?> items-center justify-center p-4">
  <div class="w-full max-w-2xl bg-white rounded-xl shadow-xl ring-1 ring-gray-200">
    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
      <h3 class="text-lg font-semibold text-gray-900">Edit Package</h3>
      <button type="button" class="text-gray-400 hover:text-gray-600" onclick="window.pkgEditClose()" aria-label="Close">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <div class="p-6">
      <?php if ($hasEdit && isset($editItem)): ?>
        <form method="post" action="<?= site_url('admin/package/edit/' . $editToken); ?>" class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="md:col-span-2">
            <label for="edit_name" class="block text-sm font-medium text-gray-700">Package Name</label>
            <input id="edit_name" name="name" type="text" value="<?= htmlspecialchars($editItem->name ?? ''); ?>" required minlength="2"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
          </div>

          <div class="md:col-span-2">
            <label for="edit_category" class="block text-sm font-medium text-gray-700">Category</label>
            <input id="edit_category" name="category" type="text" value="<?= htmlspecialchars($editItem->category ?? ''); ?>" required
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
          </div>

          <div>
            <label for="edit_hands" class="block text-sm font-medium text-gray-700">Number of Therapists</label>
            <select id="edit_hands" name="hands" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
              <option value="1" <?= ((int)($editItem->hands ?? 1) === 1) ? 'selected' : ''; ?>>1 (Solo)</option>
              <option value="2" <?= ((int)($editItem->hands ?? 1) === 2) ? 'selected' : ''; ?>>2 (4 Hand)</option>
              <option value="3" <?= ((int)($editItem->hands ?? 1) === 3) ? 'selected' : ''; ?>>3 (6 Hand)</option>
            </select>
          </div>

          <div>
            <label for="edit_duration" class="block text-sm font-medium text-gray-700">Duration (minutes)</label>
            <input id="edit_duration" name="duration" type="number" min="15" step="5" required value="<?= (int)($editItem->duration ?? 0); ?>"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-center">
          </div>

          <div>
            <label for="edit_currency" class="block text-sm font-medium text-gray-700">Currency</label>
            <input id="edit_currency" name="currency" type="text" maxlength="10" required value="<?= htmlspecialchars($editItem->currency ?? 'RM'); ?>"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
          </div>

          <div>
            <label for="edit_price_in_call" class="block text-sm font-medium text-gray-700">Price</label>
            <input id="edit_price_in_call" name="price_in_call" type="number" min="0" step="1" required value="<?= (float)($editItem->price_in_call ?? 0); ?>"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-right">
          </div>

          <div class="md:col-span-2">
            <label for="edit_description" class="block text-sm font-medium text-gray-700">Description</label>
            <textarea id="edit_description" name="description" rows="2"
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500"><?= htmlspecialchars($editItem->description ?? ''); ?></textarea>
          </div>

          <div class="md:col-span-2 flex justify-end gap-3 mt-2">
            <button type="submit" class="inline-flex items-center rounded-md bg-sky-600 text-white px-4 py-2 text-sm hover:bg-sky-700">Save</button>
            <a href="<?= site_url('admin/packages'); ?>" class="inline-flex items-center rounded-md border border-gray-300 bg-white text-gray-700 px-4 py-2 text-sm hover:bg-gray-50">Cancel</a>
          </div>
        </form>
      <?php else: ?>
        <p class="text-sm text-gray-500">Package data not found.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Create Package Modal -->
<div id="pkgCreateModalOverlay" class="fixed inset-0 bg-black/40 hidden"></div>
<div id="pkgCreateModal"
     role="dialog"
     aria-modal="true"
     aria-hidden="true"
     class="fixed inset-0 z-50 hidden items-center justify-center p-4">
  <div class="w-full max-w-2xl bg-white rounded-xl shadow-xl ring-1 ring-gray-200">
    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
      <h3 class="text-lg font-semibold text-gray-900">Add Package</h3>
      <button type="button" class="text-gray-400 hover:text-gray-600" onclick="window.pkgCreateClose()" aria-label="Close">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <div class="p-6">
      <form method="post" action="<?= site_url('admin/package/create'); ?>" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="md:col-span-2">
          <label for="create_name" class="block text-sm font-medium text-gray-700">Package Name</label>
          <input id="create_name" name="name" type="text" required minlength="2" placeholder="Example: Solo Package A"
                 class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
        </div>

        <div class="md:col-span-2">
          <label for="create_category" class="block text-sm font-medium text-gray-700">Category</label>
          <input id="create_category" name="category" type="text" required placeholder="Example: Solo Oil Relaxing Massage"
                 class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
        </div>

        <div>
          <label for="create_hands" class="block text-sm font-medium text-gray-700">Number of Therapists</label>
          <select id="create_hands" name="hands" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
            <option value="1">1 (Solo)</option>
            <option value="2">2 (4 Hand)</option>
          </select>
        </div>

        <div>
          <label for="create_duration" class="block text-sm font-medium text-gray-700">Duration (minutes)</label>
          <input id="create_duration" name="duration" type="number" min="15" step="5" required placeholder="60"
                 class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
        </div>

        <div>
          <label for="create_currency" class="block text-sm font-medium text-gray-700">Currency</label>
          <input id="create_currency" name="currency" type="text" maxlength="10" value="RM" placeholder="Example: RM"
                 class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
        </div>

        <div>
          <label for="create_price_in_call" class="block text-sm font-medium text-gray-700">Price</label>
          <input id="create_price_in_call" name="price_in_call" type="number" min="0" step="1" required placeholder="Example: 89"
                 class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-right">
        </div>

        <div class="md:col-span-2">
          <label for="create_description" class="block text-sm font-medium text-gray-700">Description</label>
          <textarea id="create_description" name="description" rows="2" placeholder="Package description (example: Full Body Massage + Manhood)"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500"></textarea>
        </div>

        <div class="md:col-span-2 flex justify-end gap-3 mt-2">
          <button type="submit" class="inline-flex items-center rounded-md bg-sky-600 text-white px-4 py-2 text-sm hover:bg-sky-700">Add</button>
          <button type="button" class="inline-flex items-center rounded-md border border-gray-300 bg-white text-gray-700 px-4 py-2 text-sm hover:bg-gray-50" onclick="window.pkgCreateClose()">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  // Modal helpers
  window.pkgEditOpen = function() {
    var m = document.getElementById('pkgEditModal');
    var o = document.getElementById('pkgEditModalOverlay');
    if (m && o) {
      m.classList.remove('hidden'); m.classList.add('flex');
      o.classList.remove('hidden');
      m.setAttribute('aria-hidden', 'false');
    }
  };
  window.pkgEditClose = function() {
    var m = document.getElementById('pkgEditModal');
    var o = document.getElementById('pkgEditModalOverlay');
    if (m && o) {
      m.classList.add('hidden'); m.classList.remove('flex');
      o.classList.add('hidden');
      m.setAttribute('aria-hidden', 'true');
    }
  };

  // Create modal helpers
  window.pkgCreateOpen = function() {
    var m = document.getElementById('pkgCreateModal');
    var o = document.getElementById('pkgCreateModalOverlay');
    if (m && o) {
      m.classList.remove('hidden'); m.classList.add('flex');
      o.classList.remove('hidden');
      m.setAttribute('aria-hidden', 'false');
    }
  };
  window.pkgCreateClose = function() {
    var m = document.getElementById('pkgCreateModal');
    var o = document.getElementById('pkgCreateModalOverlay');
    if (m && o) {
      m.classList.add('hidden'); m.classList.remove('flex');
      o.classList.add('hidden');
      m.setAttribute('aria-hidden', 'true');
    }
  };

  // ESC & overlay click to close
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      if (typeof window.pkgEditClose === 'function') window.pkgEditClose();
      if (typeof window.pkgCreateClose === 'function') window.pkgCreateClose();
    }
  });
  document.addEventListener('click', function(e){
    if (e.target && e.target.id === 'pkgEditModalOverlay') {
      if (typeof window.pkgEditClose === 'function') window.pkgEditClose();
    }
    if (e.target && e.target.id === 'pkgCreateModalOverlay') {
      if (typeof window.pkgCreateClose === 'function') window.pkgCreateClose();
    }
  });
</script>

<?php $this->load->view('admin/layout/footer'); ?>