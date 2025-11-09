<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title><?= isset($title) ? htmlspecialchars($title) : 'Bukti Pemesanan'; ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: { extend: { colors: { primary: { DEFAULT: '#0ea5e9' } } } }
    }
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    html, body { font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, Arial, 'Helvetica Neue', sans-serif; }
    .badge { display:inline-flex; align-items:center; gap:6px; padding:4px 10px; border-radius:9999px; font-weight:600; font-size:12px; }
  </style>
</head>
<body class="bg-slate-50">
  <div class="max-w-3xl mx-auto px-4 py-8">
    <header class="mb-6">
      <h1 class="text-2xl md:text-3xl font-bold text-slate-800">Bukti Pemesanan</h1>
      <p class="text-slate-600 mt-1">Tunjukkan halaman ini sebagai bukti. Admin akan menghubungi Anda.</p>
    </header>

    <?php if (!empty($error)): ?>
      <div class="mb-4 rounded-md bg-red-50 p-4 ring-1 ring-red-200">
        <p class="text-sm text-red-700"><?= htmlspecialchars($error); ?></p>
      </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
      <div class="mb-4 rounded-md bg-green-50 p-4 ring-1 ring-green-200">
        <p class="text-sm text-green-700"><?= htmlspecialchars($success); ?></p>
      </div>
    <?php endif; ?>

    <section class="rounded-2xl bg-white shadow ring-1 ring-slate-200 overflow-hidden">
      <div class="p-6 md:p-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
          <div>
            <div class="text-xs uppercase tracking-wide text-slate-500">Nomor Invoice</div>
            <div class="text-lg font-semibold text-slate-800"><?= htmlspecialchars($invoice->invoice_number ?? '-'); ?></div>
          </div>
          <div class="flex items-center gap-2">
            <?php $ps = $invoice->payment_status ?? 'DP'; ?>
            <?php if (($expired ?? false) && $ps !== 'Lunas'): ?>
              <span class="badge bg-red-100 text-red-700 ring-1 ring-red-200">Kedaluwarsa</span>
            <?php else: ?>
              <?php if ($ps === 'Lunas'): ?>
                <span class="badge bg-emerald-100 text-emerald-700 ring-1 ring-emerald-200">Lunas</span>
              <?php else: ?>
                <span class="badge bg-amber-100 text-amber-700 ring-1 ring-amber-200">Menunggu Pembayaran (DP)</span>
              <?php endif; ?>
            <?php endif; ?>
          </div>
        </div>

        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="rounded-lg border border-slate-200 p-4">
            <div class="text-sm text-slate-500">Berlaku Hingga</div>
            <div class="mt-1 font-semibold text-slate-800" id="expires_at_text">
              <?= htmlspecialchars($expires_at ?? '-'); ?>
            </div>
            <?php if (!($expired ?? false) && !empty($expires_at)): ?>
              <div class="mt-2 text-sm text-slate-600">Hitung Mundur: <span id="countdown" class="font-semibold text-slate-800">--:--:--</span></div>
              <input type="hidden" id="expires_at_value" value="<?= htmlspecialchars($expires_at); ?>">
            <?php endif; ?>
            <?php if (($expired ?? false) && ($invoice->payment_status ?? 'DP') !== 'Lunas'): ?>
              <div class="mt-2 text-sm text-red-600">Masa pembayaran 1 jam telah berakhir. Silakan lakukan pemesanan baru atau hubungi admin.</div>
            <?php endif; ?>
          </div>
          <div class="rounded-lg border border-slate-200 p-4">
            <div class="text-sm text-slate-500">Total</div>
            <div class="mt-1 font-semibold text-slate-800">
              <?php
                $curr = $booking->currency ?? 'RM';
                $total = isset($invoice->total) ? (float)$invoice->total : 0;
                echo htmlspecialchars($curr).' '.number_format($total, 0, ',', '.');
              ?>
            </div>
            <div class="mt-2 text-xs text-slate-500">Harga berdasarkan paket dan tipe panggilan yang dipilih.</div>
          </div>
        </div>

        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="rounded-lg border border-slate-200 p-4">
            <div class="text-sm text-slate-500">Detail Pemesan</div>
            <div class="mt-1 text-slate-800">
              <div><?= htmlspecialchars($booking->customer_name ?? '-'); ?></div>
              <div class="text-sm text-slate-600"><?= htmlspecialchars($booking->address ?? '-'); ?></div>
            </div>
          </div>
          <div class="rounded-lg border border-slate-200 p-4">
            <div class="text-sm text-slate-500">Detail Jadwal</div>
            <div class="mt-1 text-slate-800">
              <div>Tanggal: <?= htmlspecialchars($booking->date ?? '-'); ?></div>
              <div>Jam: <?= isset($booking->time) ? htmlspecialchars(substr($booking->time,0,5)) : '-'; ?></div>
              <div>Tipe: <?= (isset($booking->call_type) && $booking->call_type === 'OUT') ? 'Luar Premis' : 'Di Premis'; ?></div>
            </div>
          </div>
        </div>

        <div class="mt-6 rounded-lg border border-slate-200 p-4">
          <div class="text-sm text-slate-500">Paket</div>
          <div class="mt-1 text-slate-800">
            <div><?= htmlspecialchars($booking->package_name ?? '-'); ?></div>
            <div class="text-sm text-slate-600">Terapis: <?= htmlspecialchars($booking->therapist_name ?? 'Tidak ditentukan'); ?></div>
          </div>
        </div>

        <div class="mt-6 flex flex-wrap gap-3">
          <button type="button" id="copyInvoice" class="inline-flex items-center rounded-md bg-primary px-4 py-2 text-white font-semibold shadow-sm hover:bg-sky-600 focus:outline-none">
            Salin Nomor Invoice
          </button>
          <?php
            $waMessage = rawurlencode('Halo Admin, saya ingin konfirmasi pemesanan. Nomor Invoice: '.($invoice->invoice_number ?? '-'));
          ?>
          <a target="_blank" rel="noopener" href="https://wa.me/6281123332894?text=<?= $waMessage; ?>" class="inline-flex items-center rounded-md border border-slate-300 bg-white px-4 py-2 text-slate-700 font-semibold shadow-sm hover:bg-slate-50">
            Hubungi Admin via WhatsApp
          </a>
          <a href="<?= site_url('booking/form'); ?>" class="inline-flex items-center rounded-md border border-slate-300 bg-white px-4 py-2 text-slate-700 font-semibold shadow-sm hover:bg-slate-50">
            Buat Pemesanan Baru
          </a>
        </div>

        <div class="mt-6 text-xs text-slate-500">
          Masa berlaku invoice adalah 1 jam sejak dibuat. Setelah itu, jadwal dapat dibuka kembali untuk pelanggan lain.
        </div>
      </div>
    </section>
  </div>

  <script>
    // Countdown timer
    (function(){
      const val = document.getElementById('expires_at_value');
      const out = document.getElementById('countdown');
      if (!val || !out) return;
      const target = new Date(val.value.replace(' ', 'T')).getTime();

      function tick(){
        const now = Date.now();
        let diff = Math.max(0, target - now);
        const h = Math.floor(diff / (1000*60*60));
        diff -= h*(1000*60*60);
        const m = Math.floor(diff / (1000*60));
        diff -= m*(1000*60);
        const s = Math.floor(diff / 1000);
        out.textContent = String(h).padStart(2,'0')+':'+String(m).padStart(2,'0')+':'+String(s).padStart(2,'0');
        if (target - now <= 0) {
          out.textContent = '00:00:00';
          clearInterval(timer);
          // Optionally show expired badge message
          location.reload();
        }
      }
      const timer = setInterval(tick, 1000);
      tick();
    })();

    // Copy invoice number
    (function(){
      const btn = document.getElementById('copyInvoice');
      if (!btn) return;
      btn.addEventListener('click', async function(){
        try {
          const text = '<?= addslashes($invoice->invoice_number ?? "-"); ?>';
          await navigator.clipboard.writeText(text);
          btn.textContent = 'Disalin';
          setTimeout(()=>{ btn.textContent = 'Salin Nomor Invoice'; }, 1500);
        } catch (e) {
          alert('Gagal menyalin.');
        }
      });
    })();
  </script>
</body>
</html>