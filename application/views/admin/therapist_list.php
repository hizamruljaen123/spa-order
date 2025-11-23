<?php
$this->load->view('admin/layout/header', ['title' => isset($title) ? $title : 'Therapist Data']);
?>

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

<!-- Toolbar: Add Therapist (use modal) -->
<div class="mb-4 flex justify-end">
  <button type="button" onclick="window.thrCreateOpen()" class="inline-flex items-center rounded-md bg-sky-600 text-white px-4 py-2 text-sm hover:bg-sky-700">Add Therapist</button>
</div>

<!-- Therapist List -->
<div class="rounded-lg border border-gray-600 bg-gray-800 shadow-sm">
  <div class="px-5 py-3 border-b border-gray-600">
    <h2 class="text-base font-semibold text-gray-200">Therapist List</h2>
  </div>
  <div class="p-0">
    <div class="overflow-x-auto">
      <table class="min-w-full table-auto">
        <thead class="bg-gray-700">
          <tr class="text-left text-sm text-gray-200">
            <th class="px-5 py-3 w-12">#</th>
            <th class="px-5 py-3 w-20">Photo</th>
            <th class="px-5 py-3">Name</th>
            <th class="px-5 py-3">Phone Number</th>
            <th class="px-5 py-3 w-40">Status</th>
            <th class="px-5 py-3 w-56 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-700">
          <?php if (!empty($therapists)): ?>
            <?php $i = 1; foreach ($therapists as $t): ?>
              <?php $isEdit = false; ?>
              <tr class="text-sm bg-gray-800 hover:bg-gray-750">
                <td class="px-5 py-3 text-gray-200"><?= $i++; ?></td>
                <td class="px-5 py-3">
                  <?php if (!empty($t->photo)): ?>
                    <img src="<?= base_url($t->photo); ?>" alt="<?= htmlspecialchars($t->name); ?>" class="h-12 w-12 rounded object-cover border border-gray-600">
                  <?php else: ?>
                    <div class="h-12 w-12 rounded bg-gray-700 border border-gray-600 flex items-center justify-center text-xs text-gray-400">No Photo</div>
                  <?php endif; ?>
                </td>
                <td class="px-5 py-3">
                  <?php if ($isEdit): ?>
                    <form method="post" action="<?= site_url('admin/therapist/edit/' . (isset($t->token) ? $t->token : (int)$t->id)); ?>" class="grid grid-cols-1 md:grid-cols-2 gap-2 items-center">
                      <input type="text" name="name" class="rounded-md border-gray-600 bg-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-slate-100" value="<?= htmlspecialchars($t->name); ?>" required minlength="2">
                      <input type="text" name="phone" class="rounded-md border-gray-600 bg-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-slate-100" value="<?= htmlspecialchars($t->phone ?? ''); ?>">
                  <?php else: ?>
                    <div class="font-medium text-gray-200"><?= htmlspecialchars($t->name); ?></div>
                  <?php endif; ?>
                </td>
                <td class="px-5 py-3 text-gray-300">
                  <?php if (!$isEdit): ?>
                    <?= htmlspecialchars($t->phone ?? '-'); ?>
                  <?php endif; ?>
                </td>
                <td class="px-5 py-3">
                  <?php if ($isEdit): ?>
                    <select name="status" class="rounded-md border-gray-600 bg-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-slate-100" required>
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
                    <button type="submit" class="inline-flex items-center rounded-md bg-sky-600 text-white px-3 py-1.5 text-xs hover:bg-sky-700">Save</button>
                    <a href="<?= site_url('admin/therapists'); ?>" class="inline-flex items-center rounded-md border border-gray-600 bg-gray-700 text-gray-200 px-3 py-1.5 text-xs hover:bg-gray-600">Cancel</a>
                    </form>
                  <?php else: ?>
                    <a class="inline-flex items-center rounded-md border border-gray-600 bg-gray-700 text-gray-200 px-3 py-1.5 text-xs hover:bg-gray-600" href="<?= site_url('admin/therapist/edit/' . (isset($t->token) ? $t->token : (int)$t->id)); ?>">Edit</a>
                    <a class="inline-flex items-center rounded-md border border-red-600 bg-gray-700 text-red-300 px-3 py-1.5 text-xs hover:bg-red-700 hover:border-red-700" href="<?= site_url('admin/therapist/delete/' . (isset($t->token) ? $t->token : (int)$t->id)); ?>" onclick="return confirm('Delete this therapist?');">Delete</a>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" class="px-5 py-6 text-center text-gray-400">No therapist data yet.</td>
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
<div id="thrEditModalOverlay" class="fixed inset-0 bg-black/60 <?= $hasEdit ? '' : 'hidden'; ?>"></div>
<div id="thrEditModal"
     role="dialog"
     aria-modal="true"
     aria-hidden="<?= $hasEdit ? 'false' : 'true'; ?>"
     class="fixed inset-0 z-50 <?= $hasEdit ? 'flex' : 'hidden'; ?> items-center justify-center p-4">
  <div class="w-full max-w-xl bg-gray-800 rounded-xl shadow-xl ring-1 ring-gray-600">
    <div class="px-6 py-4 border-b border-gray-700 flex items-center justify-between">
      <h3 class="text-lg font-semibold text-gray-100">Edit Therapist</h3>
      <button type="button" class="text-gray-400 hover:text-gray-200" onclick="window.thrEditClose()" aria-label="Close">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <div class="p-6">
      <?php if ($hasEdit && isset($editItem)): ?>
        <form method="post" action="<?= site_url('admin/therapist/edit/' . $editToken); ?>" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="md:col-span-2">
            <label for="edit_name" class="block text-sm font-medium text-gray-300">Name</label>
            <input id="edit_name" name="name" type="text" value="<?= htmlspecialchars($editItem->name ?? ''); ?>" required minlength="2"
                   class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-slate-100">
          </div>

          <div>
            <label for="edit_phone" class="block text-sm font-medium text-gray-300">Phone Number</label>
            <input id="edit_phone" name="phone" type="text" value="<?= htmlspecialchars($editItem->phone ?? ''); ?>"
                   class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-slate-100">
          </div>

          <div>
            <label for="edit_status" class="block text-sm font-medium text-gray-300">Status</label>
            <select id="edit_status" name="status" class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-slate-100" required>
              <option value="available" <?= ($editItem->status ?? '') === 'available' ? 'selected' : ''; ?>>available</option>
              <option value="busy" <?= ($editItem->status ?? '') === 'busy' ? 'selected' : ''; ?>>busy</option>
              <option value="off" <?= ($editItem->status ?? '') === 'off' ? 'selected' : ''; ?>>off</option>
            </select>
          </div>

          <div class="md:col-span-2">
            <label for="edit_photo" class="block text-sm font-medium text-gray-300">Photo (optional)</label>
            <?php if (!empty($editItem->photo)): ?>
              <div class="flex items-center gap-3 mt-1">
                <img src="<?= base_url($editItem->photo); ?>" alt="Photo" class="h-16 w-16 rounded object-cover border border-gray-600">
                <span class="text-xs text-gray-400 truncate"><?= htmlspecialchars($editItem->photo); ?></span>
              </div>
            <?php endif; ?>
            <input id="edit_photo" name="photo" type="file" accept="image/*" class="mt-2 block w-full text-sm text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-sky-700 file:text-sky-200 hover:file:bg-sky-600">
            <p class="mt-1 text-xs text-gray-400">Format: jpg, jpeg, png, webp, gif. Max 2MB.</p>
          </div>

          <div class="md:col-span-2 flex justify-end gap-3 mt-2">
            <button type="submit" class="inline-flex items-center rounded-md bg-sky-600 text-white px-4 py-2 text-sm hover:bg-sky-700">Save</button>
            <a href="<?= site_url('admin/therapists'); ?>" class="inline-flex items-center rounded-md border border-gray-600 bg-gray-700 text-gray-200 px-4 py-2 text-sm hover:bg-gray-600">Cancel</a>
          </div>
        </form>
      <?php else: ?>
        <p class="text-sm text-gray-400">Therapist data not found.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Create Therapist Modal -->
<div id="thrCreateModalOverlay" class="fixed inset-0 bg-black/60 hidden"></div>
<div id="thrCreateModal"
     role="dialog"
     aria-modal="true"
     aria-hidden="true"
     class="fixed inset-0 z-50 hidden items-center justify-center p-4">
  <div class="w-full max-w-xl bg-gray-800 rounded-xl shadow-xl ring-1 ring-gray-600">
    <div class="px-6 py-4 border-b border-gray-700 flex items-center justify-between">
      <h3 class="text-lg font-semibold text-gray-100">Add Therapist</h3>
      <button type="button" class="text-gray-400 hover:text-gray-200" onclick="window.thrCreateClose()" aria-label="Close">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <div class="p-6">
      <form method="post" action="<?= site_url('admin/therapist/create'); ?>" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="md:col-span-2">
          <label for="create_name" class="block text-sm font-medium text-gray-300">Name</label>
          <input id="create_name" name="name" type="text" required minlength="2" placeholder="Therapist name"
                 class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-slate-100">
        </div>

        <div>
          <label for="create_phone" class="block text-sm font-medium text-gray-300">Phone Number</label>
          <input id="create_phone" name="phone" type="text" placeholder="01xxxxxxxx"
                 class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-slate-100">
        </div>

        <div>
          <label for="create_status" class="block text-sm font-medium text-gray-300">Status</label>
          <select id="create_status" name="status" required class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-slate-100">
            <option value="available">available</option>
            <option value="busy">busy</option>
            <option value="off">off</option>
          </select>
        </div>

        <div class="md:col-span-2">
          <label for="create_photo" class="block text-sm font-medium text-gray-300">Photo</label>
          <input id="create_photo" name="photo" type="file" accept="image/*" class="mt-1 block w-full text-sm text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-sky-700 file:text-sky-200 hover:file:bg-sky-600">
          <p class="mt-1 text-xs text-gray-400">Format: jpg, jpeg, png, webp, gif. Max 2MB.</p>
        </div>

        <div class="md:col-span-2 flex justify-end gap-3 mt-2">
          <button type="submit" class="inline-flex items-center rounded-md bg-sky-600 text-white px-4 py-2 text-sm hover:bg-sky-700">Add</button>
          <button type="button" class="inline-flex items-center rounded-md border border-gray-600 bg-gray-700 text-gray-200 px-4 py-2 text-sm hover:bg-gray-600" onclick="window.thrCreateClose()">Cancel</button>
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