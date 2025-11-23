<?php
$this->load->view('admin/layout/header', ['title' => isset($title) ? $title : 'Ad Form']);
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

<!-- Form Container -->
<div class="rounded-lg border border-gray-600 bg-gray-800 shadow-sm">
  <div class="px-5 py-3 border-b border-gray-700">
    <h2 class="text-base font-semibold text-gray-200"><?= isset($ad) ? 'Edit Ad' : 'Add New Ad'; ?></h2>
  </div>
  <div class="p-6">
    <?php echo form_open(isset($ad) ? 'admin/ad_management/update/' . $ad['id'] : 'admin/ad_management/store', ['enctype' => 'multipart/form-data', 'class' => 'grid grid-cols-1 md:grid-cols-2 gap-4']); ?>
      
      <div class="md:col-span-2">
        <label for="title" class="block text-sm font-medium text-gray-300">Title</label>
        <?php echo form_input('title', set_value('title', isset($ad) ? $ad['title'] : ''), [
          'class' => 'mt-1 block w-full rounded-md border-gray-600 bg-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-slate-100',
          'id' => 'title',
          'required' => 'required'
        ]); ?>
        <?php echo form_error('title', '<div class="mt-1 text-sm text-red-400">', '</div>'); ?>
      </div>

      <div class="md:col-span-2">
        <label for="image_url" class="block text-sm font-medium text-gray-300">Image</label>
        <?php echo form_upload('image_url', set_value('image_url'), [
          'class' => 'mt-1 block w-full text-sm text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-sky-700 file:text-sky-200 hover:file:bg-sky-600',
          'id' => 'image_url'
        ]); ?>
        <small class="mt-1 text-xs text-gray-400">Allowed file types: jpg, jpeg, png, gif. Maximum size: 2MB.</small>
        <?php echo form_error('image_url', '<div class="mt-1 text-sm text-red-400">', '</div>'); ?>
      </div>

      <div class="md:col-span-2">
        <label for="link_url" class="block text-sm font-medium text-gray-300">Link URL</label>
        <?php echo form_input('link_url', set_value('link_url', isset($ad) ? $ad['link_url'] : ''), [
          'class' => 'mt-1 block w-full rounded-md border-gray-600 bg-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-slate-100',
          'id' => 'link_url',
          'required' => 'required'
        ]); ?>
        <?php echo form_error('link_url', '<div class="mt-1 text-sm text-red-400">', '</div>'); ?>
      </div>

      <div>
        <label for="display_order" class="block text-sm font-medium text-gray-300">Display Order</label>
        <?php echo form_input('display_order', set_value('display_order', isset($ad) ? $ad['display_order'] : 0), [
          'class' => 'mt-1 block w-full rounded-md border-gray-600 bg-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-slate-100',
          'id' => 'display_order',
          'type' => 'number',
          'min' => '0'
        ]); ?>
      </div>

      <div>
        <div class="flex items-center">
          <?php
            $is_active_checked = isset($ad) ? $ad['is_active'] : set_value('is_active', 1);
            echo form_checkbox('is_active', '1', $is_active_checked, [
              'class' => 'h-4 w-4 text-sky-600 border-gray-600 rounded focus:ring-sky-500 bg-gray-700',
              'id' => 'is_active'
            ]);
          ?>
          <label for="is_active" class="ml-2 block text-sm text-gray-300">Active</label>
        </div>
      </div>

      <div class="md:col-span-2">
        <?php if (isset($ad) && !empty($ad['image_url'])): ?>
          <label class="block text-sm font-medium text-gray-300">Current Image</label>
          <div class="flex items-center gap-3 mt-1">
            <img src="<?= base_url($ad['image_url']); ?>" alt="Ad Image" class="h-20 w-20 rounded object-cover border border-gray-600">
            <span class="text-xs text-gray-400 truncate max-w-xs"><?= htmlspecialchars($ad['image_url']); ?></span>
          </div>
        <?php endif; ?>
      </div>

      <div class="md:col-span-2 flex justify-end gap-3 mt-2">
        <button type="submit" class="inline-flex items-center rounded-md bg-sky-600 text-white px-4 py-2 text-sm hover:bg-sky-700">Save</button>
        <a href="<?= site_url('admin/ad_management'); ?>" class="inline-flex items-center rounded-md border border-gray-600 bg-gray-700 text-gray-200 px-4 py-2 text-sm hover:bg-gray-600">Cancel</a>
      </div>
      
    <?php echo form_close(); ?>
  </div>
</div>

<?php $this->load->view('admin/layout/footer'); ?>