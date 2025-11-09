<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title><?= isset($title) ? htmlspecialchars($title) : 'Spa Home'; ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: { DEFAULT: '#0ea5e9' }, // sky-500
            brand: { DEFAULT: '#14b8a6' } // teal-500
          }
        }
      }
    }
  </script>

  <!-- Modern font -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    html, body { font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, Arial, 'Helvetica Neue', sans-serif; }
  </style>
</head>
<body class="bg-slate-50">
  <!-- App-like top bar -->
  <header class="sticky top-0 z-30 bg-white/90 backdrop-blur border-b border-slate-200">
    <div class="mx-auto max-w-6xl px-4 py-3 flex items-center justify-between">
      <div class="flex items-center gap-2">
        <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-teal-500 text-white">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a7 7 0 00-7 7c0 5 7 13 7 13s7-8 7-13a7 7 0 00-7-7zm0 9.5a2.5 2.5 0 110-5 2.5 2.5 0 010 5z"/></svg>
        </span>
        <div>
          <div class="text-base font-bold text-slate-800">SPA Management</div>
          <div class="text-xs text-slate-500">Relaks & Segar Kembali</div>
        </div>
      </div>
      <a href="<?= site_url('booking/form'); ?>" class="hidden sm:inline-flex items-center rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-sky-600">
        Tempah Sekarang
      </a>
    </div>
  </header>

  <!-- Hero -->
 <section class="relative">
  <!-- Background image + overlay -->
  <div class="absolute inset-0 -z-10">
    <img 
      src="<?= base_url('assets/img/01.jpg'); ?>" 
      alt="Spa hero" 
      class="w-full h-full object-cover"
    >
    <div class="absolute inset-0 bg-black/50"></div>
  </div>

  <!-- Content -->
  <div class="mx-auto max-w-6xl px-4 py-24 md:py-32">
    <div class="max-w-xl text-white">
      <h1 class="text-3xl md:text-5xl font-bold">Nikmati Pengalaman Spa Premium</h1>
      <p class="mt-4 text-white/90">
        Perkhidmatan lengkap dengan terapis profesional, suasana yang selesa, dan harga telus.
      </p>
      <div class="mt-8">
        <a href="#packages" class="inline-flex items-center rounded-lg bg-white/95 px-4 py-2 text-slate-900 font-semibold shadow-sm hover:bg-white">
          Terokai Pakej
        </a>
        <a href="<?= site_url('booking/form'); ?>" class="ml-3 inline-flex items-center rounded-lg bg-primary px-4 py-2 text-white font-semibold shadow-sm hover:bg-sky-600">
          Tempah Sekarang
        </a>
      </div>
    </div>
  </div>
</section>


  <!-- Content -->
  <main class="mx-auto max-w-6xl px-4 pb-24 -mt-6 md:-mt-12">
    <!-- Quick info cards -->
    
    <!-- Packages / Services -->
    <section id="packages" class="mt-8 md:mt-12">
      <div class="mb-4 md:mb-6 flex items-center justify-between">
        <h2 class="text-xl md:text-2xl font-bold text-slate-800">Pakej & Perkhidmatan</h2>
        <a href="<?= site_url('booking/form'); ?>" class="hidden sm:inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
          Buat Tempahan
        </a>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
        <?php if (!empty($packages)): ?>
          <?php foreach ($packages as $p): ?>
            <div class="group rounded-2xl bg-white overflow-hidden shadow-sm ring-1 ring-slate-200 hover:shadow-md transition">
              
              <div class="p-4">
                <?php if (!empty($p->description)): ?>
                  <p class="text-sm text-slate-600 line-clamp-2 mb-2"><?= htmlspecialchars($p->description); ?></p>
                <?php else: ?>
                  <p class="text-sm text-slate-600 mb-2">Pakej spa untuk relaksasi tubuh dan minda.</p>
                <?php endif; ?>
                <div class="mb-2 text-xs text-slate-600">
                  <span class="inline-flex items-center gap-2">
                    <span class="rounded-full bg-slate-100 px-2 py-0.5">Kategori: <?= htmlspecialchars($p->category ?? '-'); ?></span>
                    <span class="rounded-full bg-slate-100 px-2 py-0.5">Terapis: <?= isset($p->hands) ? (int)$p->hands : 1; ?></span>
                  </span>
                </div>
                <div class="flex items-center justify-between">
                  <div class="text-primary font-bold">
                    <?php
                      $curr = isset($p->currency) ? $p->currency : 'RM';
                      $pin  = isset($p->price_in_call) ? (float)$p->price_in_call : null;
                      $pout = isset($p->price_out_call) ? (float)$p->price_out_call : null;
                    ?>
                    <?php if ($pin !== null || $pout !== null): ?>
                      <span class="inline-flex items-center gap-2">
                        <?php if ($pin !== null): ?>
                          <span>Di Premis: <?= htmlspecialchars($curr); ?> <?= number_format($pin, 0, ',', '.'); ?></span>
                        <?php endif; ?>
                        <?php if ($pout !== null): ?>
                          <span class="text-slate-600">•</span>
                          <span>Luar Premis: <?= htmlspecialchars($curr); ?> <?= number_format($pout, 0, ',', '.'); ?></span>
                        <?php endif; ?>
                      </span>
                    <?php else: ?>
                      Hubungi kami
                    <?php endif; ?>
                  </div>
                  <a href="<?= site_url('booking/form'); ?>" class="inline-flex items-center rounded-lg bg-primary px-3 py-1.5 text-sm font-semibold text-white hover:bg-sky-600">
                    Tempah
                  </a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="col-span-full rounded-xl border border-dashed border-slate-300 bg-white p-6 text-center text-slate-600">
            Pakej belum tersedia. Sila kembali lagi nanti.
          </div>
        <?php endif; ?>
      </div>
    </section>

    <!-- Senarai harga dihapus mengikut arahan -->

    <!-- Address & Contact -->
    <section id="contact" class="mt-10 md:mt-14">
  <h2 class="text-xl md:text-2xl font-bold text-slate-800 mb-4 md:mb-6">Alamat & Hubungi</h2>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
    <!-- Info Kontak -->
    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
      <div class="text-slate-700">
        <div class="font-semibold text-slate-800">Alamat</div>
        <p class="text-sm text-slate-600 mt-1">21-1, Jalan Abadi 2/1</p>
        <p class="text-sm text-slate-600">Abadi Heights, Puchong, Selangor 71420</p>
        <p class="text-sm text-slate-600">Malaysia</p>

        <div class="mt-4 font-semibold text-slate-800">Kontak</div>
        <p class="text-sm text-slate-600 mt-1">
          Tel:
          <a href="tel:+60380619349" class="text-blue-600 hover:underline">+603 8061 9349</a> /
          <a href="tel:+601123332894" class="text-blue-600 hover:underline">+60 11 2333 2894</a>
        </p>
        <p class="text-sm text-slate-600">
          Email:
          <a href="mailto:apittmenspa@outlook.com" class="text-blue-600 hover:underline">
            apittmenspa@outlook.com
          </a>
        </p>
      </div>

      <a
        href="<?= site_url('booking/form'); ?>"
        class="mt-4 inline-flex items-center rounded-lg bg-brand px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-teal-600"
      >
        Buat Tempahan
      </a>
    </div>

    <!-- Gambar -->
    
  </div>
</section>

  </main>

  <!-- Bottom sticky CTA (mobile-first) -->
  <div class="fixed inset-x-0 bottom-0 z-30 border-t border-slate-200 bg-white/95 backdrop-blur">
    <div class="mx-auto max-w-6xl px-4 py-3 flex items-center justify-between">
      <div class="text-sm">
        <div class="font-semibold text-slate-800">Sedia untuk bersantai?</div>
        <div class="text-slate-500">Tempah jadual anda sekarang</div>
      </div>
      <a href="<?= site_url('booking/form'); ?>" class="inline-flex items-center rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-sky-600">
        Tempah Sekarang
      </a>
    </div>
  </div>
</body>
</html>