<?php
// Use the admin layout header
$this->load->view('admin/layout/header', ['title' => isset($title) ? $title : 'Exclusive Treatments']);

// Prepare values
$treatments = isset($treatments) ? $treatments : [];
$editItemId = isset($editItemId) ? $editItemId : null;
$editItem = isset($editItem) ? $editItem : null;

// Flash messages
if (!empty($flash['success'])): ?>
  <div class="mb-4 rounded-md bg-green-900/50 p-4 text-sm text-green-300 border border-green-800">
    <?= htmlspecialchars($flash['success']); ?>
  </div>
<?php endif; ?>
<?php if (!empty($flash['error'])): ?>
  <div class="mb-4 rounded-md bg-red-900/50 p-4 text-sm text-red-300 border border-red-800">
    <?= htmlspecialchars($flash['error']); ?>
  </div>
<?php endif; ?>

<!-- Inline Edit Modal -->
<?php if ($editItemId && $editItem): ?>
<div id="editModal" class="fixed inset-0 bg-gray-900 bg-opacity-80 flex items-center justify-center z-50">
  <div class="bg-gray-800 rounded-lg shadow-lg max-w-md w-full mx-4">
    <div class="px-6 py-4 border-b border-gray-700">
      <h3 class="text-lg font-semibold text-gray-100">Edit Exclusive Treatment</h3>
    </div>
    <form action="<?= site_url('admin/exclusive_treatment_edit/' . ($editItem->token ?? $editItemId)); ?>" method="post" class="p-6 space-y-4">
      <div>
        <label for="edit_name" class="block text-sm font-medium text-gray-300">Treatment Name</label>
        <input type="text" id="edit_name" name="name" value="<?= htmlspecialchars($editItem->name ?? ''); ?>" class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-slate-100" required>
      </div>
      <div>
        <label for="edit_description" class="block text-sm font-medium text-gray-300">Description</label>
        <textarea id="edit_description" name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-slate-100"><?= htmlspecialchars($editItem->description ?? ''); ?></textarea>
      </div>
      <div>
        <label for="edit_price" class="block text-sm font-medium text-gray-300">Price</label>
        <input type="number" step="0.01" id="edit_price" name="price" value="<?= htmlspecialchars($editItem->price ?? ''); ?>" class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-slate-100" required>
      </div>
      <div>
        <label for="edit_currency" class="block text-sm font-medium text-gray-300">Currency</label>
        <input type="text" id="edit_currency" name="currency" value="<?= htmlspecialchars($editItem->currency ?? 'RM'); ?>" class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-slate-100" maxlength="10">
      </div>
      <div>
        <label for="edit_category" class="block text-sm font-medium text-gray-300">Category</label>
        <input type="text" id="edit_category" name="category" value="<?= htmlspecialchars($editItem->category ?? ''); ?>" class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-slate-100" required>
      </div>
      <div>
        <label for="edit_display_order" class="block text-sm font-medium text-gray-300">Display Order</label>
        <input type="number" id="edit_display_order" name="display_order" value="<?= htmlspecialchars($editItem->display_order ?? 0); ?>" class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-slate-100">
      </div>
      <div class="flex items-center">
        <input type="checkbox" id="edit_is_add_on" name="is_add_on" value="1" <?= ($editItem->is_add_on ?? 0) ? 'checked' : ''; ?> class="h-4 w-4 text-sky-600 focus:ring-sky-500 border-gray-600 rounded bg-gray-700">
        <label for="edit_is_add_on" class="ml-2 block text-sm text-gray-200">Add-on treatment</label>
      </div>
      <div class="flex items-center">
        <input type="checkbox" id="edit_is_active" name="is_active" value="1" <?= ($editItem->is_active ?? 1) ? 'checked' : ''; ?> class="h-4 w-4 text-sky-600 focus:ring-sky-500 border-gray-600 rounded bg-gray-700">
        <label for="edit_is_active" class="ml-2 block text-sm text-gray-200">Active</label>
      </div>
      <div class="flex justify-end space-x-3 pt-4">
        <a href="<?= site_url('admin/exclusive_treatments'); ?>" class="inline-flex items-center px-4 py-2 border border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-200 bg-gray-700 hover:bg-gray-600">Cancel</a>
        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-sky-600 hover:bg-sky-700">Save</button>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<!-- Main Content -->
<div class="space-y-6">
  <!-- Header with Add Button -->
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-bold text-gray-200">Exclusive Treatments</h1>
      <p class="mt-1 text-sm text-gray-300">Manage the list of exclusive treatments offered</p>
    </div>
    <button type="button" onclick="showAddModal()" class="inline-flex items-center rounded-md bg-sky-600 text-white px-4 py-2 text-sm font-semibold hover:bg-sky-700">
      <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
      </svg>
      Add Treatment
    </button>
  </div>

  <!-- Treatments Table -->
  <div class="rounded-lg border border-gray-600 bg-gray-800 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-700">
        <thead class="bg-gray-700">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Treatment</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Category</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Price</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Type</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-200 uppercase tracking-wider">Status</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-200 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-gray-800 divide-y divide-gray-700">
          <?php if (!empty($treatments)): ?>
            <?php foreach ($treatments as $treatment): ?>
              <tr class="hover:bg-gray-750">
                <td class="px-6 py-4">
                  <div class="flex items-center">
                    <div>
                      <div class="text-sm font-medium text-gray-200">
                        <?= htmlspecialchars($treatment['name']); ?>
                      </div>
                      <?php if (!empty($treatment['description'])): ?>
                        <div class="text-sm text-gray-400 line-clamp-2 max-w-xs">
                          <?= htmlspecialchars($treatment['description']); ?>
                        </div>
                      <?php endif; ?>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-900/50 text-blue-300 capitalize">
                    <?= htmlspecialchars($treatment['category']); ?>
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-200">
                  <?= htmlspecialchars($treatment['currency']); ?> <?= number_format($treatment['price'], 2, ',', '.'); ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <?php if ($treatment['is_add_on']): ?>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-900/50 text-purple-300">
                      Add-on
                    </span>
                  <?php else: ?>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-900/50 text-green-300">
                      Main
                    </span>
                  <?php endif; ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <?php if ($treatment['is_active']): ?>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-900/50 text-green-300">
                      Active
                    </span>
                  <?php else: ?>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-700 text-gray-200">
                      Inactive
                    </span>
                  <?php endif; ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <div class="flex items-center justify-end space-x-2">
                    <a href="<?= site_url('admin/exclusive_treatment_edit/' . ($treatment['id'] ?? $treatment['id'])); ?>" class="text-sky-400 hover:text-sky-300">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                      </svg>
                    </a>
                    <button onclick="confirmDelete('<?= site_url('admin/exclusive_treatment_delete/' . ($treatment['id'] ?? $treatment['id'])); ?>')" class="text-red-400 hover:text-red-300">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" class="px-6 py-12 text-center">
                <div class="text-gray-400">
                  <svg class="mx-auto h-12 w-12 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                  </svg>
                  <h3 class="mt-2 text-sm font-medium text-gray-300">No exclusive treatments</h3>
                  <p class="mt-1 text-sm text-gray-400">Get started by adding your first exclusive treatment.</p>
                  <div class="mt-6">
                    <button type="button" onclick="showAddModal()" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-sky-600 hover:bg-sky-700">
                      <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                      </svg>
                      Add Treatment
                    </button>
                  </div>
                </div>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Add Modal -->
<div id="addModal" class="fixed inset-0 bg-gray-900 bg-opacity-80 hidden items-center justify-center z-50">
  <div class="bg-gray-800 rounded-lg shadow-lg max-w-md w-full mx-4">
    <div class="px-6 py-4 border-b border-gray-700">
      <h3 class="text-lg font-semibold text-gray-100">Add Exclusive Treatment</h3>
    </div>
    <form action="<?= site_url('admin/exclusive_treatment_create'); ?>" method="post" class="p-6 space-y-4">
      <div>
        <label for="name" class="block text-sm font-medium text-gray-300">Treatment Name</label>
        <input type="text" id="name" name="name" class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-slate-100" required>
      </div>
      <div>
        <label for="description" class="block text-sm font-medium text-gray-300">Description</label>
        <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-slate-100"></textarea>
      </div>
      <div>
        <label for="price" class="block text-sm font-medium text-gray-300">Price</label>
        <input type="number" step="0.01" id="price" name="price" class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-slate-100" required>
      </div>
      <div>
        <label for="currency" class="block text-sm font-medium text-gray-300">Currency</label>
        <input type="text" id="currency" name="currency" value="RM" class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-slate-100" maxlength="10">
      </div>
      <div>
        <label for="category" class="block text-sm font-medium text-gray-300">Category</label>
        <select id="category" name="category" class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-slate-100" required>
          <option value="bekam">Bekam</option>
          <option value="treatment">Treatment</option>
          <option value="add-on">Add-on</option>
        </select>
      </div>
      <div>
        <label for="display_order" class="block text-sm font-medium text-gray-300">Display Order</label>
        <input type="number" id="display_order" name="display_order" value="0" class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-slate-100">
      </div>
      <div class="flex items-center">
        <input type="checkbox" id="is_add_on" name="is_add_on" value="1" class="h-4 w-4 text-sky-600 focus:ring-sky-500 border-gray-600 rounded bg-gray-700">
        <label for="is_add_on" class="ml-2 block text-sm text-gray-200">Add-on treatment</label>
      </div>
      <div class="flex items-center">
        <input type="checkbox" id="is_active" name="is_active" value="1" checked class="h-4 w-4 text-sky-600 focus:ring-sky-500 border-gray-600 rounded bg-gray-700">
        <label for="is_active" class="ml-2 block text-sm text-gray-200">Active</label>
      </div>
      <div class="flex justify-end space-x-3 pt-4">
        <button type="button" onclick="hideAddModal()" class="inline-flex items-center px-4 py-2 border border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-200 bg-gray-700 hover:bg-gray-600">Cancel</button>
        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-sky-600 hover:bg-sky-700">Add</button>
      </div>
    </form>
  </div>
</div>

<script>
function showAddModal() {
  document.getElementById('addModal').classList.remove('hidden');
  document.getElementById('addModal').classList.add('flex');
}

function hideAddModal() {
  document.getElementById('addModal').classList.add('hidden');
  document.getElementById('addModal').classList.remove('flex');
}

function confirmDelete(url) {
  if (confirm('Are you sure you want to delete this treatment?')) {
    window.location.href = url;
  }
}

// Auto-show edit modal if editing
<?php if ($editItemId): ?>
document.addEventListener('DOMContentLoaded', function() {
  // Modal is already shown via PHP
});
<?php endif; ?>
</script>

<?php
// Footer
$this->load->view('admin/layout/footer');
?>