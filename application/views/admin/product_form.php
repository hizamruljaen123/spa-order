<?php $this->load->view('admin/layout/header', ['title' => isset($title) ? $title : 'Borang Produk']); ?>

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

<!-- Form Container -->
<div class="rounded-lg border border-gray-200 bg-white shadow-sm">
  <div class="px-5 py-3 border-b border-gray-200">
    <h2 class="text-base font-semibold text-gray-900"><?= isset($product) ? 'Sunting Produk' : 'Tambah Produk Baru'; ?></h2>
  </div>
  <div class="p-6">
    <?php echo form_open(isset($product) ? 'admin/product_management/update/' . $product['id'] : 'admin/product_management/store', ['enctype' => 'multipart/form-data', 'class' => 'grid grid-cols-1 md:grid-cols-2 gap-4']); ?>

      <div class="md:col-span-2">
        <label for="name" class="block text-sm font-medium text-gray-700">Nama Produk</label>
        <?php echo form_input('name', set_value('name', isset($product) ? $product['name'] : ''), [
          'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500',
          'id' => 'name',
          'required' => 'required'
        ]); ?>
        <?php echo form_error('name', '<div class="mt-1 text-sm text-red-600">', '</div>'); ?>
      </div>

      <div class="md:col-span-2">
        <label for="description" class="block text-sm font-medium text-gray-700">Keterangan</label>
        <?php echo form_textarea('description', set_value('description', isset($product) ? $product['description'] : ''), [
          'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500',
          'id' => 'description',
          'rows' => 3
        ]); ?>
        <?php echo form_error('description', '<div class="mt-1 text-sm text-red-600">', '</div>'); ?>
      </div>

      <div>
        <label for="price" class="block text-sm font-medium text-gray-700">Harga</label>
        <?php echo form_input('price', set_value('price', isset($product) ? $product['price'] : ''), [
          'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500',
          'id' => 'price',
          'type' => 'number',
          'step' => '0.01',
          'min' => '0',
          'required' => 'required'
        ]); ?>
        <?php echo form_error('price', '<div class="mt-1 text-sm text-red-600">', '</div>'); ?>
      </div>

      <div>
        <label for="currency" class="block text-sm font-medium text-gray-700">Mata Wang</label>
        <?php
        $currency_options = ['RM' => 'RM (Ringgit Malaysia)', 'USD' => 'USD (US Dollar)', 'EUR' => 'EUR (Euro)'];
        echo form_dropdown('currency', $currency_options, set_value('currency', isset($product) ? $product['currency'] : 'RM'), [
          'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500',
          'id' => 'currency',
          'required' => 'required'
        ]);
        ?>
        <?php echo form_error('currency', '<div class="mt-1 text-sm text-red-600">', '</div>'); ?>
      </div>

      <div class="md:col-span-2">
        <label for="image_url" class="block text-sm font-medium text-gray-700">Muat Naik Gambar</label>
        <?php echo form_upload('image_url', set_value('image_url'), [
          'class' => 'mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100',
          'id' => 'image_url',
          'accept' => 'image/*'
        ]); ?>
        <p class="mt-1 text-xs text-gray-500">Jenis fail yang dibenarkan: jpg, jpeg, png, gif. Saiz maksimum: 2MB. Muat naik gambar terus dari komputer anda.</p>
        <?php echo form_error('image_url', '<div class="mt-1 text-sm text-red-600">', '</div>'); ?>
      </div>

      <div>
        <label for="display_order" class="block text-sm font-medium text-gray-700">Susunan Paparan</label>
        <?php echo form_input('display_order', set_value('display_order', isset($product) ? $product['display_order'] : 0), [
          'class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500',
          'id' => 'display_order',
          'type' => 'number',
          'min' => '0'
        ]); ?>
      </div>

      <div>
        <div class="flex items-center">
          <?php
            $is_active_checked = isset($product) ? $product['is_active'] : set_value('is_active', 1);
            echo form_checkbox('is_active', '1', $is_active_checked, [
              'class' => 'h-4 w-4 text-sky-600 border-gray-300 rounded focus:ring-sky-500',
              'id' => 'is_active'
            ]);
          ?>
          <label for="is_active" class="ml-2 block text-sm text-gray-700">Aktif</label>
        </div>
      </div>

      <?php if (isset($product) && !empty($product['image_url'])): ?>
        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-gray-700">Gambar Semasa</label>
          <div class="flex items-center gap-3 mt-1">
            <img src="<?= base_url($product['image_url']); ?>" alt="Gambar Produk" class="h-20 w-20 rounded object-cover border border-gray-200">
            <span class="text-xs text-gray-500">Biarkan medan gambar kosong untuk mengekalkan gambar semasa.</span>
          </div>
        </div>
      <?php endif; ?>

      <div class="md:col-span-2 flex justify-end gap-3 mt-2">
        <button type="submit" class="inline-flex items-center rounded-md bg-sky-600 text-white px-4 py-2 text-sm font-semibold hover:bg-sky-700">
          Simpan Produk
        </button>
        <a href="<?= site_url('admin/product_management'); ?>" class="inline-flex items-center rounded-md border border-gray-300 bg-white text-gray-700 px-4 py-2 text-sm hover:bg-gray-50">
          Batal
        </a>
      </div>

    <?php echo form_close(); ?>
  </div>
</div>

<?php $this->load->view('admin/layout/footer'); ?>