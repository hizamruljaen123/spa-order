<?php $this->load->view('admin/layout/header', ['title' => isset($title) ? $title : 'Product List']); ?>

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

<div class="rounded-lg border border-gray-200 bg-white shadow-sm">
  <div class="px-5 py-3 border-b border-gray-200">
    <h2 class="text-base font-semibold text-gray-900">Product List</h2>
    <p class="text-sm text-gray-600 mt-1">Manage products sold in the catalog</p>
  </div>

  <div class="p-5">
    <div class="flex justify-between items-center mb-4">
      <div class="text-sm text-gray-700">
        Total products: <span class="font-semibold"><?= count($products ?? []); ?></span>
      </div>
      <a href="<?= site_url('admin/product_management/create'); ?>" class="inline-flex items-center rounded-md bg-sky-600 text-white px-4 py-2 text-sm font-semibold hover:bg-sky-700">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        Add New Product
      </a>
    </div>

    <?php if (empty($products)): ?>
      <div class="text-center py-8">
        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No products found</h3>
        <p class="text-gray-600 mb-4">Start adding your first product</p>
        <a href="<?= site_url('admin/product_management/create'); ?>" class="inline-flex items-center rounded-md bg-sky-600 text-white px-4 py-2 text-sm font-semibold hover:bg-sky-700">
          Add First Product
        </a>
      </div>
    <?php else: ?>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <?php foreach ($products as $product): ?>
              <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900">#<?= $product['id']; ?></div>
                </td>
                <td class="px-6 py-4">
                  <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($product['name']); ?></div>
                  <?php if (!empty($product['description'])): ?>
                    <div class="text-sm text-gray-600 truncate max-w-xs">
                      <?= htmlspecialchars(substr($product['description'], 0, 60)); ?><?= strlen($product['description']) > 60 ? '...' : ''; ?>
                    </div>
                  <?php endif; ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <?php if (!empty($product['image_url'])): ?>
                    <img src="<?= base_url($product['image_url']); ?>" alt="<?= htmlspecialchars($product['name']); ?>" class="h-12 w-12 rounded-lg object-cover border border-gray-200">
                  <?php else: ?>
                    <div class="h-12 w-12 rounded-lg bg-gray-200 flex items-center justify-center">
                      <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                      </svg>
                    </div>
                  <?php endif; ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900">
                    <?= htmlspecialchars($product['currency']); ?> <?= number_format($product['price'], 2, ',', '.'); ?>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <?php if ($product['is_active']): ?>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                      <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 8 8">
                        <circle cx="4" cy="4" r="3"/>
                      </svg>
                      Active
                    </span>
                  <?php else: ?>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                      <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 8 8">
                        <circle cx="4" cy="4" r="3"/>
                      </svg>
                      Inactive
                    </span>
                  <?php endif; ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    #<?= $product['display_order']; ?>
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <div class="flex justify-end space-x-2">
                    <a href="<?= site_url('admin/product_management/edit/' . $product['id']); ?>"
                       class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100">
                      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                      </svg>
                      Edit
                    </a>
                    <button onclick="confirmDelete(<?= $product['id']; ?>, '<?= htmlspecialchars(addslashes($product['name'])); ?>')"
                            class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100">
                      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                      </svg>
                      Delete
                    </button>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<script>
function confirmDelete(productId, productName) {
  if (confirm('Are you sure you want to delete product "' + productName + '"? This action cannot be undone.')) {
    window.location.href = '<?= site_url('admin/product_management/delete/'); ?>' + productId;
  }
}
</script>

<?php $this->load->view('admin/layout/footer'); ?>