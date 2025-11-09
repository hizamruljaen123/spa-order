<?php
$this->load->view('admin/layout/header', ['title' => isset($title) ? $title : 'Data Therapist']);
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

<!-- Add Therapist -->
<div class="rounded-lg border border-gray-200 bg-white shadow-sm mb-6">
  <div class="px-5 py-3 border-b border-gray-200">
    <h2 class="text-base font-semibold text-gray-900">Tambah Therapist</h2>
  </div>
  <div class="p-5">
    <form class="grid grid-cols-1 md:grid-cols-3 gap-4" method="post" action="<?= site_url('admin/therapist/create'); ?>">
      <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Nama</label>
        <input type="text" id="name" name="name" required minlength="2" placeholder="Nama therapist" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
      </div>
      <div>
        <label for="phone" class="block text-sm font-medium text-gray-700">No HP</label>
        <input type="text" id="phone" name="phone" placeholder="0812xxxxxxx" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
      </div>
      <div>
        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
        <select id="status" name="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500">
          <option value="available">available</option>
          <option value="busy">busy</option>
          <option value="off">off</option>
        </select>
      </div>
      <div class="md:col-span-3 flex justify-end">
        <button type="submit" class="inline-flex items-center rounded-md bg-sky-600 text-white px-4 py-2 text-sm hover:bg-sky-700">Tambah</button>
      </div>
    </form>
  </div>
</div>

<!-- List Therapist -->
<div class="rounded-lg border border-gray-200 bg-white shadow-sm">
  <div class="px-5 py-3 border-b border-gray-200">
    <h2 class="text-base font-semibold text-gray-900">Daftar Therapist</h2>
  </div>
  <div class="p-0">
    <div class="overflow-x-auto">
      <table class="min-w-full table-auto">
        <thead class="bg-gray-50">
          <tr class="text-left text-sm text-gray-600">
            <th class="px-5 py-3 w-12">#</th>
            <th class="px-5 py-3">Nama</th>
            <th class="px-5 py-3">No HP</th>
            <th class="px-5 py-3 w-40">Status</th>
            <th class="px-5 py-3 w-56 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <?php if (!empty($therapists)): ?>
            <?php $i = 1; foreach ($therapists as $t): ?>
              <?php $isEdit = isset($editItemId) && (int)$editItemId === (int)$t->id; ?>
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
                    <a class="inline-flex items-center rounded-md border border-gray-300 bg-white text-gray-700 px-3 py-1.5 text-xs hover:bg-gray-50" href="<?= site_url('admin/therapist/edit/' . (isset($t->token) ? $t->token : (int)$t->id)); ?>">Edit</a>
                    <a class="inline-flex items-center rounded-md border border-red-300 bg-white text-red-600 px-3 py-1.5 text-xs hover:bg-red-50" href="<?= site_url('admin/therapist/delete/' . (isset($t->token) ? $t->token : (int)$t->id)); ?>" onclick="return confirm('Hapus therapist ini?');">Hapus</a>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="5" class="px-5 py-6 text-center text-gray-500">Belum ada data therapist.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php $this->load->view('admin/layout/footer'); ?>