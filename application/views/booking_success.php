<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?= isset($title) ? htmlspecialchars($title) : 'Success Booking'; ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = { theme: { extend: { colors: { primary: { DEFAULT: '#0ea5e9' } } } } };
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    html, body { font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, Arial, 'Helvetica Neue', sans-serif; }
  </style>
</head>
<body class="bg-slate-50">
  <div class="max-w-3xl mx-auto px-4 py-8">
    <header class="mb-6">
      <h1 class="text-2xl md:text-3xl font-bold text-slate-800">Booking Successful</h1>
      <p class="text-slate-600 mt-1">Thank you. Use the WhatsApp button below to contact admin. This page will automatically redirect to WhatsApp in 5 minutes.</p>
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
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="rounded-lg border border-slate-200 p-4">
            <div class="text-sm text-slate-500">Invoice Number</div>
            <div class="mt-1 font-semibold text-slate-800"><?= htmlspecialchars($invoice->invoice_number ?? '-'); ?></div>
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
          </div>
        </div>

        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="rounded-lg border border-slate-200 p-4">
            <div class="text-sm text-slate-500">Customer Details</div>
            <div class="mt-1 text-slate-800">
              <div><?= htmlspecialchars($booking->customer_name ?? '-'); ?></div>
              <div class="text-sm text-slate-600"><?= htmlspecialchars($booking->address ?? '-'); ?></div>
            </div>
          </div>
          <div class="rounded-lg border border-slate-200 p-4">
            <div class="text-sm text-slate-500">Schedule Details</div>
            <div class="mt-1 text-slate-800">
              <div>Date: <?= htmlspecialchars($booking->date ?? '-'); ?></div>
              <div>Time: <?= isset($booking->time) ? htmlspecialchars(substr($booking->time,0,5)) : '-'; ?></div>
              <div>Type: <?= (isset($booking->call_type) && $booking->call_type === 'OUT') ? 'Out Premise' : 'At Premise'; ?></div>
            </div>
          </div>
        </div>

        <div class="mt-6 rounded-lg border border-slate-200 p-4">
          <div class="text-sm text-slate-500">Package</div>
          <div class="mt-1 text-slate-800">
            <div><?= htmlspecialchars($booking->package_name ?? '-'); ?></div>
            <div class="text-sm text-slate-600">Therapist: <?= htmlspecialchars($booking->therapist_name ?? 'Not specified'); ?></div>
          </div>
        </div>

        <div class="mt-6 flex flex-wrap gap-3 items-center">
          <?php
            $waAdmin = '60143218026';
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
          <a target="_blank" rel="noopener" href="https://wa.me/<?= $waAdmin; ?>?text=<?= $waMessage; ?>" class="inline-flex items-center rounded-md bg-green-600 px-4 py-2 text-white font-semibold shadow-sm hover:bg-green-700">
            Contact Admin via WhatsApp
          </a>
          <?php if (!empty($tokenEnc)): ?>
            <a href="<?= site_url('booking/invoice/'.$tokenEnc); ?>" class="inline-flex items-center rounded-md border border-slate-300 bg-white px-4 py-2 text-slate-700 font-semibold shadow-sm hover:bg-slate-50">
              View Order Proof
            </a>
          <?php endif; ?>
        </div>

        <div class="mt-4 text-xs text-slate-500">
          This page will automatically redirect to WhatsApp in 5 minutes for easy confirmation.
        </div>
      </div>
    </section>
  </div>

  <script>
    (function(){
      // Auto redirect to WA after 5 minutes (300000 ms)
      var redirectMs = 300000;
      var waUrl = "https://wa.me/<?= $waAdmin; ?>?text=<?= $waMessage; ?>";
      setTimeout(function(){
        window.location.href = waUrl;
      }, redirectMs);
    })();
  </script>
</body>
</html>