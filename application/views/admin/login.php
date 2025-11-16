<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= isset($title) ? $title : 'Admin Login' ?></title>
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
          <p class="mt-2 text-sm text-white/80">Booking & relaxation management portal.</p>
        </div>
      </div>
    </section>
    <section class="flex items-center justify-center p-6">
      <div class="w-full max-w-md bg-white rounded-xl shadow-xl ring-1 ring-gray-200 p-8">
        <h1 class="text-2xl font-semibold text-gray-900">Admin Login</h1>
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
            <div class="relative mt-2">
              <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 7.5a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a8.25 8.25 0 1115 0" />
                </svg>
              </span>
              <input
                type="text"
                id="username"
                name="username"
                value="<?= set_value('username'); ?>"
                autocomplete="username"
                autofocus
                required
                placeholder="Username"
                aria-invalid="<?= form_error('username') ? 'true' : 'false' ?>"
                class="peer w-full rounded-2xl bg-gray-50/80 pl-11 pr-4 py-3 text-gray-900 placeholder-transparent shadow-sm transition caret-brand focus:bg-white focus:outline-none <?= form_error('username') ? 'border border-red-300 ring-2 ring-red-200 focus:border-red-400 focus:ring-red-300' : 'border border-gray-200 ring-2 ring-transparent focus:border-brand focus:ring-brand/30' ?>">
              <label for="username" class="pointer-events-none absolute left-11 top-1/2 -translate-y-1/2 text-sm text-gray-500 transition-all peer-focus:top-2 peer-focus:text-xs peer-focus:text-brand peer-placeholder-shown:text-sm peer-[&:not(:placeholder-shown)]:top-2 peer-[&:not(:placeholder-shown)]:text-xs peer-[&:not(:placeholder-shown)]:text-brand">Username</label>
              <?php if (form_error('username')): ?>
                <p class="mt-2 text-xs text-red-600"><?= strip_tags(form_error('username')); ?></p>
              <?php endif; ?>
            </div>
          </div>
          <div>
            <div class="relative mt-2">
              <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V7.5a4.5 4.5 0 10-9 0v3M5.25 10.5h13.5a.75.75 0 01.75.75v8.25A2.25 2.25 0 0117.25 22.5H6.75A2.25 2.25 0 014.5 20.25V11.25a.75.75 0 01.75-.75" />
                </svg>
              </span>
              <input
                type="password"
                id="password"
                name="password"
                autocomplete="current-password"
                required
                placeholder="Password"
                aria-invalid="<?= form_error('password') ? 'true' : 'false' ?>"
                class="peer w-full rounded-2xl bg-gray-50/80 pl-11 pr-11 py-3 text-gray-900 placeholder-transparent shadow-sm transition caret-brand focus:bg-white focus:outline-none <?= form_error('password') ? 'border border-red-300 ring-2 ring-red-200 focus:border-red-400 focus:ring-red-300' : 'border border-gray-200 ring-2 ring-transparent focus:border-brand focus:ring-brand/30' ?>">
              <label for="password" class="pointer-events-none absolute left-11 top-1/2 -translate-y-1/2 text-sm text-gray-500 transition-all peer-focus:top-2 peer-focus:text-xs peer-focus:text-brand peer-placeholder-shown:text-sm peer-[&:not(:placeholder-shown)]:top-2 peer-[&:not(:placeholder-shown)]:text-xs peer-[&:not(:placeholder-shown)]:text-brand">Password</label>
              <button type="button" id="togglePassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none" aria-label="Show password">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-7.5 9.75-7.5S21.75 12 21.75 12 18 19.5 12 19.5 2.25 12 2.25 12z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </button>
              <?php if (form_error('password')): ?>
                <p class="mt-2 text-xs text-red-600"><?= strip_tags(form_error('password')); ?></p>
              <?php endif; ?>
            </div>
          </div>
          <div class="flex items-center gap-3">
            <button type="submit" class="inline-flex items-center justify-center rounded-md bg-brand px-4 py-2 text-white font-semibold shadow-sm hover:bg-indigo-600 focus:outline-none">Login</button>
            <a href="<?= site_url('booking'); ?>" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">Back</a>
          </div>
        </form>

        <script>
          (function(){
            const btn = document.getElementById('togglePassword');
            const input = document.getElementById('password');
            if (btn && input) {
              btn.addEventListener('click', function(){
                const isPwd = input.getAttribute('type') === 'password';
                input.setAttribute('type', isPwd ? 'text' : 'password');
                this.setAttribute('aria-label', isPwd ? 'Hide password' : 'Show password');
              });
            }
          })();
        </script>

        <p class="mt-6 text-sm text-gray-500">
          Default credentials: <strong>admin</strong> / <strong>admin123</strong>. Please change in database after login.
        </p>
        <p class="mt-2 text-xs text-gray-400">Forgot password? Contact system administrator.</p>
      </div>
    </section>
  </div>
</body>
</html>