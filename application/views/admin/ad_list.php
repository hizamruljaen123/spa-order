<?php
$this->load->view('admin/layout/header', ['title' => isset($title) ? $title : 'Manage Ads']);
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

<!-- Toolbar: Add Ad -->
<div class="mb-4 flex justify-end">
  <a href="<?= site_url('admin/ad_management/create'); ?>" class="inline-flex items-center rounded-md bg-sky-600 text-white px-4 py-2 text-sm hover:bg-sky-700">Add Ad</a>
</div>

<!-- List Advertisements -->
<div class="rounded-lg border border-gray-200 bg-white shadow-sm">
  <div class="px-5 py-3 border-b border-gray-200">
    <h2 class="text-base font-semibold text-gray-900">Ad List</h2>
  </div>
  <div class="p-0">
    <div class="overflow-x-auto">
      <table class="min-w-full table-auto">
        <thead class="bg-gray-50">
          <tr class="text-left text-sm text-gray-600">
            <th class="px-5 py-3 w-12">#</th>
            <th class="px-5 py-3">Title</th>
            <th class="px-5 py-3 w-32">Image</th>
            <th class="px-5 py-3">Link</th>
            <th class="px-5 py-3 w-24">Status</th>
            <th class="px-5 py-3 w-16">Order</th>
            <th class="px-5 py-3 w-40 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <?php if (!empty($ads)): ?>
            <?php $i = 1; foreach ($ads as $ad): ?>
              <tr class="text-sm">
                <td class="px-5 py-3"><?= $i++; ?></td>
                <td class="px-5 py-3">
                  <div class="font-medium text-gray-900"><?= htmlspecialchars($ad['title']); ?></div>
                </td>
                <td class="px-5 py-3">
                  <?php if (!empty($ad['image_url'])): ?>
                    <img src="<?= base_url($ad['image_url']); ?>" alt="<?= htmlspecialchars($ad['title']); ?>" class="h-16 w-16 rounded object-cover border border-gray-200">
                  <?php else: ?>
                    <div class="h-16 w-16 rounded bg-gray-100 border border-gray-200 flex items-center justify-center text-xs text-gray-500">No Image</div>
                  <?php endif; ?>
                </td>
                <td class="px-5 py-3">
                  <div class="text-gray-600 truncate max-w-xs" title="<?= htmlspecialchars($ad['link_url']); ?>">
                    <?= htmlspecialchars($ad['link_url']); ?>
                  </div>
                </td>
                <td class="px-5 py-3">
                  <?php
                    $badgeClass = 'bg-gray-500';
                    if ($ad['is_active']) $badgeClass = 'bg-emerald-600';
                    else $badgeClass = 'bg-gray-400';
                  ?>
                  <span class="inline-flex items-center px-2 py-1 rounded text-white text-xs <?= $badgeClass; ?>">
                    <?= $ad['is_active'] ? 'Active' : 'Inactive'; ?>
                  </span>
                </td>
                <td class="px-5 py-3">
                  <span class="inline-flex items-center px-2 py-1 rounded bg-gray-100 text-gray-700 text-xs">
                    <?= $ad['display_order']; ?>
                  </span>
                </td>
                <td class="px-5 py-3 text-right">
                  <a class="inline-flex items-center rounded-md border border-gray-300 bg-white text-gray-700 px-3 py-1.5 text-xs hover:bg-gray-50" href="<?= site_url('admin/ad_management/edit/' . $ad['id']); ?>">Edit</a>
                  <a class="inline-flex items-center rounded-md border border-red-300 bg-white text-red-600 px-3 py-1.5 text-xs hover:bg-red-50" href="<?= site_url('admin/ad_management/delete/' . $ad['id']); ?>" onclick="return confirm('Delete this ad?');">Delete</a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" class="px-5 py-6 text-center text-gray-500">No ad data available.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php $this->load->view('admin/layout/footer'); ?>