<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?= isset($title) ? htmlspecialchars($title) : 'Order Proof'; ?></title>
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
<body class="bg-gray-900">
  <div class="max-w-3xl mx-auto px-4 py-8">
    <header class="mb-6">
      <h1 class="text-2xl md:text-3xl font-bold text-slate-100">Order Proof</h1>
      <p class="text-slate-300 mt-1">Show this page as proof. Admin will contact you.</p>
    </header>

    <?php if (!empty($error)): ?>
      <div class="mb-4 rounded-md bg-red-900/50 p-4 ring-1 ring-red-800">
        <p class="text-sm text-red-300"><?= htmlspecialchars($error); ?></p>
      </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
      <div class="mb-4 rounded-md bg-green-900/50 p-4 ring-1 ring-green-800">
        <p class="text-sm text-green-300"><?= htmlspecialchars($success); ?></p>
      </div>
    <?php endif; ?>

    <section class="rounded-2xl bg-gray-800 shadow ring-1 ring-gray-700 overflow-hidden">
      <div class="p-6 md:p-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
          <div>
            <div class="text-xs uppercase tracking-wide text-slate-400">Invoice Number</div>
            <div class="text-lg font-semibold text-slate-100"><?= htmlspecialchars($invoice->invoice_number ?? '-'); ?></div>
          </div>
          <div class="flex items-center gap-2">
            <?php $ps = $invoice->payment_status ?? 'DP'; ?>
            <?php if (($expired ?? false) && $ps !== 'Paid'): ?>
              <span class="badge bg-red-900/50 text-red-300 ring-1 ring-red-800">Expired</span>
            <?php else: ?>
              <?php if ($ps === 'Paid'): ?>
                <span class="badge bg-green-900/50 text-green-300 ring-1 ring-green-800">Paid</span>
              <?php else: ?>
                <span class="badge bg-yellow-900/50 text-yellow-300 ring-1 ring-yellow-800">Awaiting Payment (DP)</span>
              <?php endif; ?>
            <?php endif; ?>
          </div>
        </div>

        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="rounded-lg border border-gray-700 p-4">
            <div class="text-sm text-slate-400">Valid Until</div>
            <div class="mt-1 font-semibold text-slate-100" id="expires_at_text">
              <?= htmlspecialchars($expires_at ?? '-'); ?>
            </div>
            <?php if (!($expired ?? false) && !empty($expires_at)): ?>
              <div class="mt-2 text-sm text-slate-300">Countdown: <span id="countdown" class="font-semibold text-slate-100">--:--:--</span></div>
              <input type="hidden" id="expires_at_value" value="<?= htmlspecialchars($expires_at); ?>">
            <?php endif; ?>
            <?php if (($expired ?? false) && ($invoice->payment_status ?? 'DP') !== 'Paid'): ?>
              <div class="mt-2 text-sm text-red-400">Payment time of 1 hour has ended. Please make a new booking or contact admin.</div>
            <?php endif; ?>
          </div>
          <div class="rounded-lg border border-gray-700 p-4">
            <div class="text-sm text-slate-400">Total</div>
            <div class="mt-1 font-semibold text-slate-100">
              <?php
                $curr = $booking->currency ?? 'RM';
                $total = isset($invoice->total) ? (float)$invoice->total : 0;
                echo htmlspecialchars($curr).' '.number_format($total, 0, ',', '.');
              ?>
            </div>
            <div class="mt-2 text-xs text-slate-400">Price based on package and call type selected.</div>
          </div>
        </div>

        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="rounded-lg border border-gray-700 p-4">
            <div class="text-sm text-slate-400">Customer Details</div>
            <div class="mt-1 text-slate-100">
              <div><?= htmlspecialchars($booking->customer_name ?? '-'); ?></div>
              <div class="text-sm text-slate-300"><?= htmlspecialchars($booking->address ?? '-'); ?></div>
            </div>
          </div>
          <div class="rounded-lg border border-gray-700 p-4">
            <div class="text-sm text-slate-400">Schedule Details</div>
            <div class="mt-1 text-slate-100">
              <div>Date: <?= htmlspecialchars($booking->date ?? '-'); ?></div>
              <div>Time: <?= isset($booking->time) ? htmlspecialchars(substr($booking->time,0,5)) : '-'; ?></div>
              <div>Type: <?= (isset($booking->call_type) && $booking->call_type === 'OUT') ? 'Out Premise' : 'At Premise'; ?></div>
            </div>
          </div>
        </div>

        <div class="mt-6 rounded-lg border border-gray-700 p-4">
          <div class="text-sm text-slate-400">Package</div>
          <div class="mt-1 text-slate-100">
            <div><?= htmlspecialchars($booking->package_name ?? '-'); ?></div>
            <div class="text-sm text-slate-300">Therapist: <?= htmlspecialchars($booking->therapist_name ?? 'Not specified'); ?></div>
          </div>
        </div>

        <div class="mt-6 flex flex-wrap gap-3">
          <button type="button" id="copyInvoice" class="inline-flex items-center rounded-md bg-primary px-4 py-2 text-white font-semibold shadow-sm hover:bg-sky-600 focus:outline-none">
            Copy Invoice Number
          </button>
          <?php
            // Build WhatsApp message from booking details to the provided admin number
            $waAdmin = '60143218026'; // wa.me requires number without leading '+'
            $cust   = isset($booking->customer_name) ? $booking->customer_name : '-';
            $addr   = isset($booking->address) ? $booking->address : '-';
            $pack   = isset($booking->package_name) ? $booking->package_name : '-';
            $thera  = (isset($booking->therapist_name) && $booking->therapist_name) ? $booking->therapist_name : 'Not specified';
            $ctype  = (isset($booking->call_type) && $booking->call_type === 'OUT') ? 'Out Premise' : 'At Premise';
            $dateV  = isset($booking->date) ? $booking->date : '-';
            $timeV  = isset($booking->time) ? substr($booking->time,0,5) : '-';
            $inv    = isset($invoice->invoice_number) ? $invoice->invoice_number : '-';
            $waMessage = rawurlencode(
              "Hello Admin, I want to confirm my booking:\n"
              . "Name: {$cust}\n"
              . "Address: {$addr}\n"
              . "Package: {$pack}\n"
              . "Therapist: {$thera}\n"
              . "Type: {$ctype}\n"
              . "Date: {$dateV}\n"
              . "Time: {$timeV}\n"
              . "Invoice: {$inv}"
            );
          ?>
          <a target="_blank" rel="noopener" href="https://wa.me/<?= $waAdmin; ?>?text=<?= $waMessage; ?>" class="inline-flex items-center rounded-md border border-gray-600 bg-gray-700 px-4 py-2 text-gray-200 font-semibold shadow-sm hover:bg-gray-600">
            Contact Admin via WhatsApp
          </a>
          <a href="<?= site_url('booking/form'); ?>" class="inline-flex items-center rounded-md border border-gray-600 bg-gray-700 px-4 py-2 text-gray-200 font-semibold shadow-sm hover:bg-gray-600">
            Create New Booking
          </a>
        </div>

        <div class="mt-6 text-xs text-slate-400">
          Invoice validity period is 1 hour from creation. After that, the schedule can be opened again for other customers.
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
          btn.textContent = 'Copied';
          setTimeout(()=>{ btn.textContent = 'Copy Invoice Number'; }, 1500);
        } catch (e) {
          alert('Failed to copy.');
        }
      });
    })();
  </script>
</body>
</html>