<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title><?= isset($title) ? htmlspecialchars($title) : 'Spa Booking'; ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: { DEFAULT: '#0ea5e9' } // sky-500
          }
        }
      }
    }
  </script>

  <!-- Modern, clean font -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

  <style>
    html, body {
      font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, Arial, 'Helvetica Neue', sans-serif;
    }
    .required:after { content: ' *'; color: #ef4444; }
  </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-teal-50 via-white to-sky-50">
  <!-- Soft decorative blobs -->
  <div class="relative overflow-hidden">
    <div class="pointer-events-none absolute -top-24 -left-24 h-72 w-72 rounded-full bg-teal-200 opacity-40 blur-2xl"></div>
    <div class="pointer-events-none absolute -bottom-24 -right-24 h-72 w-72 rounded-full bg-sky-200 opacity-40 blur-2xl"></div>
  </div>

  <div class="max-w-5xl mx-auto px-4 py-10 md:py-14">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="text-3xl md:text-4xl font-bold text-slate-800">Tempahan Spa</h1>
        <p class="mt-2 text-slate-500">Isi borang berikut untuk membuat tempahan perkhidmatan spa.</p>
      </div>
      <div class="hidden md:block">
        <span class="inline-flex items-center gap-2 rounded-full bg-white/70 px-3 py-2 shadow-sm ring-1 ring-slate-200">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-teal-500" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a7 7 0 00-7 7c0 5 7 13 7 13s7-8 7-13a7 7 0 00-7-7zm0 9.5a2.5 2.5 0 110-5 2.5 2.5 0 010 5z"/></svg>
          <span class="text-sm font-medium text-slate-700">Relaks & Segar Kembali</span>
        </span>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">
      <!-- Visual / Hero -->
      <div class="hidden md:block">
        <div class="relative overflow-hidden rounded-2xl shadow-lg ring-1 ring-slate-200">
          <img src="https://images.unsplash.com/photo-1515378791036-0648a3ef77b2?auto=format&fit=crop&w=1200&q=60" alt="Spa ambience" class="h-full w-full object-cover">
          <div class="absolute inset-0 bg-gradient-to-t from-black/30 via-black/10 to-transparent"></div>
          <div class="absolute bottom-0 left-0 right-0 p-5 text-white">
            <div class="text-lg font-semibold">Pengalaman Spa Premium</div>
            <p class="text-sm opacity-90">Nikmati layanan terbaik dengan therapist profesional.</p>
          </div>
        </div>
      </div>

      <!-- Form card -->
      <div>
        <div class="rounded-2xl bg-white shadow-xl ring-1 ring-slate-200">
          <div class="p-6 md:p-8">
            <?php if (!empty($error)): ?>
              <div class="mb-4 rounded-md bg-red-50 p-4 ring-1 ring-red-200">
                <p class="text-sm text-red-700"><?= htmlspecialchars($error); ?></p>
              </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
              <div class="mb-4 rounded-md bg-green-50 p-4 ring-1 ring-green-200">
                <p class="text-sm text-green-700"><?= htmlspecialchars($success); ?></p>
              </div>

              <!-- Optional payment info -->
              <div class="mb-6 rounded-lg border border-green-200 bg-white p-5">
                <div class="font-semibold text-slate-700 mb-2">Maklumat Pembayaran Deposit (Pilihan)</div>
                <ul class="text-sm text-slate-600 space-y-1">
                  <li>Bank: BCA</li>
                  <li>No. Rekening: 1234567890</li>
                  <li>Atas Nama: PT Spa Sejahtera</li>
                </ul>
                <p class="mt-2 text-xs text-slate-500">Pengesahan pembayaran boleh dilakukan melalui WhatsApp admin.</p>
              </div>
            <?php endif; ?>

            <?php if (!empty($validation)): ?>
              <div class="mb-4 rounded-md bg-yellow-50 p-4 ring-1 ring-yellow-200">
                <?= $validation; ?>
              </div>
            <?php endif; ?>

            <form method="post" action="<?= site_url('booking/submit'); ?>" novalidate>
              <div class="mb-4">
                <label class="required block text-sm font-medium text-slate-700" for="customer_name">Nama</label>
                <input type="text" class="mt-1 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary" id="customer_name" name="customer_name" required minlength="2" placeholder="Nama lengkap">
              </div>

              <div class="mb-4">
                <label class="required block text-sm font-medium text-slate-700" for="address">Alamat</label>
                <textarea class="mt-1 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary" id="address" name="address" rows="3" required minlength="5" placeholder="Alamat lengkap"></textarea>
              </div>

              <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700" for="therapist_id">Nama Terapis (pilihan)</label>
                <select class="mt-1 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary" id="therapist_id" name="therapist_id">
                  <option value="">Pilih terapis (pilihan)</option>
                  <?php if (!empty($therapists)): ?>
                    <?php foreach ($therapists as $t): ?>
                      <option value="<?= (int)$t->id; ?>">
                        <?= htmlspecialchars($t->name); ?> <?= !empty($t->status) ? '(' . htmlspecialchars($t->status) . ')' : ''; ?>
                      </option>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </select>
                <p class="mt-1 text-xs text-slate-500">Biarkan kosong jika tidak memilih terapis.</p>
              </div>

              <div class="mb-4">
                <label class="required block text-sm font-medium text-slate-700" for="package_id">Pakej</label>
                <select class="mt-1 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary" id="package_id" name="package_id" required>
                  <option value="">Pilih pakej</option>
                  <?php if (!empty($packages)): ?>
                    <?php foreach ($packages as $p): ?>
                      <option value="<?= (int)$p->id; ?>">
                        <?php
                          $curr = isset($p->currency) ? $p->currency : 'Rp';
                          $pin  = isset($p->price_in_call) ? (float)$p->price_in_call : (isset($p->price) ? (float)$p->price : 0);
                          $pout = isset($p->price_out_call) ? (float)$p->price_out_call : null;
                        ?>
                        <?= htmlspecialchars($p->name); ?>
                        <?php if (isset($p->duration)): ?> - <?= (int)$p->duration; ?> minit<?php endif; ?>
                        <?php if ($pin): ?> - IN: <?= htmlspecialchars($curr); ?> <?= number_format($pin, 0, ',', '.'); ?><?php endif; ?>
                        <?php if ($pout): ?> / OUT: <?= htmlspecialchars($curr); ?> <?= number_format($pout, 0, ',', '.'); ?><?php endif; ?>
                      </option>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </select>
              </div>

              <div class="mb-4">
                <span class="block text-sm font-medium text-slate-700">Tipe Panggilan</span>
                <div class="mt-2 flex items-center gap-4">
                  <label class="inline-flex items-center gap-2">
                    <input type="radio" name="call_type" value="IN" class="h-4 w-4 text-primary focus:ring-primary" checked>
                    <span class="text-sm text-slate-700">In Call</span>
                  </label>
                  <label class="inline-flex items-center gap-2">
                    <input type="radio" name="call_type" value="OUT" class="h-4 w-4 text-primary focus:ring-primary">
                    <span class="text-sm text-slate-700">Out Call</span>
                  </label>
                </div>
                <p class="mt-1 text-xs text-slate-500">Harga akan mengikuti tipe panggilan.</p>
              </div>


              <!-- Jadwal - Pilih Tanggal lalu Jam (berdasarkan ketersediaan) -->
              <div class="mb-4">
                <label class="required block text-sm font-medium text-slate-700" for="date">Tarikh</label>
                <input
                  type="date"
                  class="mt-1 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary"
                  id="date"
                  name="date"
                  required
                  min="<?= date('Y-m-d'); ?>"
                >
                <p class="mt-1 text-xs text-slate-500">Pilih tanggal terlebih dahulu.</p>
              </div>

              <div class="mb-4">
                <span class="required block text-sm font-medium text-slate-700">Pilih Jam</span>

                <!-- Nilai jam yang akan dikirim -->
                <input type="hidden" id="time" name="time" required>

                <!-- Slot jam akan dirender di sini -->
                <div id="slots" class="mt-2 grid grid-cols-3 sm:grid-cols-4 gap-2">
                  <!-- contoh tombol akan di-inject lewat JS -->
                </div>
                <div id="slots_help" class="mt-2 text-xs text-slate-500">Pilih jam yang tersedia (warna hijau).</div>
                <div id="slots_error" class="mt-2 hidden rounded bg-red-50 text-red-700 text-sm px-3 py-2"></div>
              </div>

              <script>
                // Render slot jam yang mudah dipahami untuk orang tua:
                // 1) Pilih tanggal
                // 2) Slot jam tampil (jam yang sudah terisi dinonaktifkan)
                // 3) Klik jam untuk memilih (akan disorot), kemudian kirim
                window.addEventListener('load', function () {
                  const therapistSel = document.getElementById('therapist_id');
                  const dateInput    = document.getElementById('date');
                  const timeInput    = document.getElementById('time');
                  const slotsWrap    = document.getElementById('slots');
                  const slotsError   = document.getElementById('slots_error');
                  const submitBtn    = document.querySelector('button[type="submit"]');

                  const ENDPOINT = '<?= site_url('booking/availability'); ?>';

                  let selected = null;

                  function renderLoading() {
                    slotsWrap.innerHTML = '';
                    for (let i = 0; i < 6; i++) {
                      const s = document.createElement('div');
                      s.className = 'h-10 rounded-md bg-slate-200 animate-pulse';
                      slotsWrap.appendChild(s);
                    }
                  }

                  function setSubmitEnabled(enabled) {
                    if (!submitBtn) return;
                    submitBtn.disabled = !enabled;
                    if (enabled) {
                      submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    } else {
                      submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    }
                  }

                  function renderSlots(data) {
                    slotsWrap.innerHTML = '';
                    slotsError.classList.add('hidden');
                    const booked = new Set(data.booked || []);
                    (data.slots || []).forEach(function (t) {
                      const btn = document.createElement('button');
                      const isBooked = booked.has(t);
                      btn.type = 'button';
                      btn.textContent = t;
                      btn.className =
                        'h-10 rounded-md text-sm font-medium border px-3 ' +
                        (isBooked
                          ? 'bg-slate-100 text-slate-400 border-slate-200 cursor-not-allowed line-through'
                          : 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100 focus:outline-none focus:ring-2 focus:ring-emerald-400');
                      btn.disabled = isBooked;

                      if (!isBooked) {
                        btn.addEventListener('click', function () {
                          selected = t;
                          // Backend menerima HH:MM atau HH:MM:SS; normalisasi ke HH:MM:SS
                          timeInput.value = t.length === 5 ? (t + ':00') : t;
                          // Sorot tombol terpilih
                          Array.prototype.forEach.call(slotsWrap.querySelectorAll('button'), function (b) {
                            b.classList.remove('ring-2', 'ring-emerald-500', 'bg-emerald-200');
                          });
                          btn.classList.add('ring-2', 'ring-emerald-500', 'bg-emerald-200');
                          // Aktifkan tombol submit
                          setSubmitEnabled(true);
                        });
                      }
                      slotsWrap.appendChild(btn);
                    });

                    // Jika belum pilih jam, nonaktifkan submit
                    if (!timeInput.value) {
                      setSubmitEnabled(false);
                    }
                  }

                  async function fetchSlots() {
                    const date = dateInput ? dateInput.value : '';
                    selected = null;
                    timeInput.value = '';
                    setSubmitEnabled(false);

                    if (!date) {
                      slotsWrap.innerHTML = '<div class="text-sm text-slate-500">Pilih tanggal untuk melihat jam tersedia.</div>';
                      return;
                    }

                    renderLoading();
                    const th = therapistSel ? therapistSel.value : '';
                    const url = ENDPOINT + '?date=' + encodeURIComponent(date) + (th ? '&therapist_id=' + encodeURIComponent(th) : '');
                    try {
                      const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                      if (!res.ok) throw new Error('HTTP ' + res.status);
                      const data = await res.json();
                      renderSlots(data);
                    } catch (e) {
                      slotsWrap.innerHTML = '';
                      slotsError.textContent = 'Gagal memuat ketersediaan. Coba lagi.';
                      slotsError.classList.remove('hidden');
                    }
                  }

                  if (dateInput)  dateInput.addEventListener('change', fetchSlots);
                  if (therapistSel) therapistSel.addEventListener('change', fetchSlots);

                  // Tampilkan pesan awal
                  slotsWrap.innerHTML = '<div class="text-sm text-slate-500">Pilih tanggal untuk melihat jam tersedia.</div>';
                  setSubmitEnabled(false);
                });
              </script>

              <div class="mt-6">
                <button type="submit" class="inline-flex w-full items-center justify-center rounded-md bg-primary px-4 py-3 text-base font-semibold text-white shadow-sm transition hover:bg-sky-600 focus:outline-none focus:ring-2 focus:ring-primary">
                  Hantar Tempahan
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer note -->
    <div class="mt-10 text-sm text-slate-500">
      <p class="mb-1">Selepas tempahan dihantar, notifikasi akan diteruskan kepada admin melalui Telegram.</p>
      <p class="mb-0">Admin akan mengesahkan ketersediaan jadual dan terapis.</p>
    </div>
  </div>
</body>
</html>