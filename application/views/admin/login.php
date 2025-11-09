<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= isset($title) ? $title : 'Login Admin' ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors:{
            brand:'#6a7cff'
          }
        }
      }
    }
  </script>
</head>
<body class="min-h-screen bg-gray-50">
  <div class="grid grid-cols-1 lg:grid-cols-2 min-h-screen">
    <section class="hidden lg:block relative">
      <img src="https://deerhurstresort.com/uploads/2020/02/web-bigstock-male-Deep-tissue-massage-78157139-1920x1080.jpg" alt="Spa" class="absolute inset-0 w-full h-full object-cover">
      <div class="absolute inset-0 bg-gradient-to-br from-black/40 to-black/10"></div>
      <div class="relative z-10 p-8 flex h-full items-end">
        <div class="text-white max-w-lg">
          <h2 class="text-3xl font-bold">APITT SPA</h2>
          <p class="mt-2 text-sm text-white/80">Relaxation & booking management portal.</p>
        </div>
      </div>
    </section>
    <section class="flex items-center justify-center p-6">
      <div class="w-full max-w-md bg-white rounded-xl shadow-xl ring-1 ring-gray-200 p-8">
        <h1 class="text-2xl font-semibold text-gray-900">Login Admin</h1>
        <?php if (!empty($flash['error'])): ?>
          <div class="mt-4 rounded-lg border border-red-200 bg-red-50 text-red-700 px-4 py-3">
            <?= $flash['error']; ?>
          </div>
        <?php endif; ?>
        <?php if (!empty($flash['success'])): ?>
          <div class="mt-4 rounded-lg border border-green-200 bg-green-50 text-green-700 px-4 py-3">
            <?= $flash['success']; ?>
          </div>
        <?php endif; ?>
        <?php if (validation_errors()): ?>
          <div class="mt-4 rounded-lg border border-red-200 bg-red-50 text-red-700 px-4 py-3">
            <?= validation_errors(); ?>
          </div>
        <?php endif; ?>

        <?= form_open('login', ['class' => 'mt-6 space-y-5']); ?>
          <div>
            <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
            <input type="text" id="username" name="username" value="<?= set_value('username'); ?>" autocomplete="username" required class="mt-2 w-full rounded-md border-gray-300 bg-white px-3 py-2 shadow-sm focus:border-brand focus:ring-brand">
          </div>
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input type="password" id="password" name="password" autocomplete="current-password" required class="mt-2 w-full rounded-md border-gray-300 bg-white px-3 py-2 shadow-sm focus:border-brand focus:ring-brand">
          </div>
          <div class="flex items-center gap-3">
            <button type="submit" class="inline-flex items-center justify-center rounded-md bg-brand px-4 py-2 text-white font-semibold shadow-sm hover:bg-indigo-600 focus:outline-none">Masuk</button>
            <a href="<?= site_url('booking'); ?>" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">Kembali</a>
          </div>
        </form>

        <p class="mt-6 text-sm text-gray-500">
          Default kredensial awal: <strong>admin</strong> / <strong>admin123</strong>. Silakan ganti di database setelah login.
        </p>
        <p class="mt-2 text-xs text-gray-400">Lupa password? Hubungi administrator sistem.</p>
      </div>
    </section>
  </div>
</body>
</html>