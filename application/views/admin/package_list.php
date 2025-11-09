<?php
$this->load->view('admin/layout/header', ['title' => isset($title) ? $title : 'Data Paket Spa (APITT Menu)']);
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

<!-- Add Package (new schema) -->
<div class="rounded-lg border border-gray-200 bg-white shadow-sm mb-6">
  <div class="px-5 py-3 border-b border-gray-200">
    <h2 class="text-base font-semibold text-gray-900">Tambah Paket Spa (APITT Format)</h2>
  </div>
  <div class="p-5">
    <form class="grid grid-cols-1 md:grid-cols-12 gap-4" method="post" action="<?= site_url('admin/package/create'); ?>">
      <div class="md:col-span-4">
        <label for="name" class="block text-sm font-medium text-gray-700">Nama Paket</label>
        <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500" id="name" name="name" required minlength="2" placeholder="Contoh: Solo Package A">
      </div>
      <div class="md:col-span-4">
        <label for="category" class="block text-sm font-medium text-gray-700">Kategori</label>
        <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500" id="category" name="category" required placeholder="Contoh: Solo Oil Relaxing Massage">
      </div>
      <div class="md:col-span-4">
        <label for="hands" class="block text-sm font-medium text-gray-700">Jumlah Therapist</label>
        <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500" id="hands" name="hands" required>
          <option value="1">1 (Solo)</option>
          <option value="2">2 (4 Hand)</option>
        </select>
      </div>

      <div class="md:col-span-3">
        <label for="duration" class="block text-sm font-medium text-gray-700">Durasi (menit)</label>
        <input type="number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500" id="duration" name="duration" min="15" step="5" required placeholder="60">
      </div>
      <div class="md:col-span-3">
        <label for="currency" class="block text-sm font-medium text-gray-700">Mata Uang</label>
        <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500" id="currency" name="currency" maxlength="10" value="RM" placeholder="Contoh: RM">
      </div>
      <div class="md:col-span-3">
        <label for="price_in_call" class="block text-sm font-medium text-gray-700">Harga In Call</label>
        <input type="number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-right" id="price_in_call" name="price_in_call" min="0" step="1" required placeholder="Contoh: 89">
      </div>
      <div class="md:col-span-3">
        <label for="price_out_call" class="block text-sm font-medium text-gray-700">Harga Out Call</label>
        <input type="number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-right" id="price_out_call" name="price_out_call" min="0" step="1" required placeholder="Contoh: 150">
      </div>

      <div class="md:col-span-12">
        <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
        <textarea class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500" id="description" name="description" rows="2" placeholder="Deskripsi paket (contoh: Full Body Massage + Manhood)"></textarea>
      </div>

      <div class="md:col-span-12 flex justify-end">
        <button class="inline-flex items-center rounded-md bg-sky-600 text-white px-4 py-2 text-sm hover:bg-sky-700" type="submit">Tambah</button>
      </div>
    </form>
  </div>
</div>

<!-- List Packages -->
<div class="rounded-lg border border-gray-200 bg-white shadow-sm">
  <div class="px-5 py-3 border-b border-gray-200">
    <h2 class="text-base font-semibold text-gray-900">Daftar Paket (APITT)</h2>
  </div>
  <div class="p-0">
    <div class="overflow-x-auto">
      <table class="min-w-full table-auto">
        <thead class="bg-gray-50">
          <tr class="text-left text-sm text-gray-600">
            <th class="px-5 py-3 w-12">#</th>
            <th class="px-5 py-3">Nama Paket</th>
            <th class="px-5 py-3">Kategori</th>
            <th class="px-5 py-3 text-center w-32">Therapist</th>
            <th class="px-5 py-3 text-center w-36">Durasi</th>
            <th class="px-5 py-3 text-right w-40">In Call</th>
            <th class="px-5 py-3 text-right w-40">Out Call</th>
            <th class="px-5 py-3 text-center w-28">Currency</th>
            <th class="px-5 py-3 w-[360px]">Deskripsi</th>
            <th class="px-5 py-3 text-right w-56">Aksi</th>
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
                <?php if ($isEdit): ?>
                  <form method="post" action="<?= site_url('admin/package/edit/' . (isset($p->token) ? $p->token : (int)$p->id)); ?>" class="grid grid-cols-1 gap-2">
                    <input type="text" name="name" class="rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500" value="<?= htmlspecialchars($p->name); ?>" required minlength="2">
                <?php else: ?>
                  <div class="font-medium text-gray-900"><?= htmlspecialchars($p->name); ?></div>
                <?php endif; ?>
              </td>

              <!-- Category -->
              <td class="px-5 py-3">
                <?php if ($isEdit): ?>
                  <input type="text" name="category" class="rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500" value="<?= htmlspecialchars($p->category ?? ''); ?>" required>
                <?php else: ?>
                  <div class="text-gray-600"><?= htmlspecialchars($p->category ?? '-'); ?></div>
                <?php endif; ?>
              </td>

              <!-- Hands -->
              <td class="px-5 py-3 text-center">
                <?php if ($isEdit): ?>
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
                <?php if ($isEdit): ?>
                  <input type="number" name="duration" class="w-24 text-center rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500" value="<?= (int)($p->duration ?? 0); ?>" min="15" step="5" required>
                <?php else: ?>
                  <?= (int)($p->duration ?? 0); ?> menit
                <?php endif; ?>
              </td>

              <!-- In Call -->
              <td class="px-5 py-3 text-right">
                <?php if ($isEdit): ?>
                  <input type="number" name="price_in_call" class="w-28 text-right rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500" value="<?= (float)($p->price_in_call ?? 0); ?>" min="0" step="1" required>
                <?php else: ?>
                  <?= htmlspecialchars($p->currency ?? 'RM'); ?> <?= number_format((float)($p->price_in_call ?? 0), 0, ',', '.'); ?>
                <?php endif; ?>
              </td>

              <!-- Out Call -->
              <td class="px-5 py-3 text-right">
                <?php if ($isEdit): ?>
                  <input type="number" name="price_out_call" class="w-28 text-right rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500" value="<?= (float)($p->price_out_call ?? 0); ?>" min="0" step="1" required>
                <?php else: ?>
                  <?= htmlspecialchars($p->currency ?? 'RM'); ?> <?= number_format((float)($p->price_out_call ?? 0), 0, ',', '.'); ?>
                <?php endif; ?>
              </td>

              <!-- Currency -->
              <td class="px-5 py-3 text-center">
                <?php if ($isEdit): ?>
                  <input type="text" name="currency" class="w-20 text-center rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500" value="<?= htmlspecialchars($p->currency ?? 'RM'); ?>" maxlength="10" required>
                <?php else: ?>
                  <span class="inline-flex items-center px-2 py-1 rounded bg-gray-200 text-gray-700 text-xs"><?= htmlspecialchars($p->currency ?? 'RM'); ?></span>
                <?php endif; ?>
              </td>

              <!-- Description -->
              <td class="px-5 py-3">
                <?php if ($isEdit): ?>
                  <textarea name="description" class="w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500" rows="1"><?= htmlspecialchars($p->description ?? ''); ?></textarea>
                <?php else: ?>
                  <div class="text-gray-700 truncate max-w-[340px]"><?= htmlspecialchars($p->description ?? '-'); ?></div>
                <?php endif; ?>
              </td>

              <!-- Actions -->
              <td class="px-5 py-3 text-right">
                <?php if ($isEdit): ?>
                  <button type="submit" class="inline-flex items-center rounded-md bg-sky-600 text-white px-3 py-1.5 text-xs hover:bg-sky-700">Simpan</button>
                  <a href="<?= site_url('admin/packages'); ?>" class="inline-flex items-center rounded-md border border-gray-300 bg-white text-gray-700 px-3 py-1.5 text-xs hover:bg-gray-50">Batal</a>
                  </form>
                <?php else: ?>
                  <a class="inline-flex items-center rounded-md border border-gray-300 bg-white text-gray-700 px-3 py-1.5 text-xs hover:bg-gray-50" href="<?= site_url('admin/package/edit/' . (isset($p->token) ? $p->token : (int)$p->id)); ?>">Edit</a>
                  <a class="inline-flex items-center rounded-md border border-red-300 bg-white text-red-600 px-3 py-1.5 text-xs hover:bg-red-50" href="<?= site_url('admin/package/delete/' . (isset($p->token) ? $p->token : (int)$p->id)); ?>" onclick="return confirm('Hapus paket ini?');">Hapus</a>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="10" class="px-5 py-6 text-center text-gray-500">Belum ada data paket.</td>
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

<?php $this->load->view('admin/layout/footer'); ?>