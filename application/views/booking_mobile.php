
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?= isset($title) ? htmlspecialchars($title) : 'Spa Booking (Mobile)'; ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: { DEFAULT: '#0ea5e9' }
          }
        }
      }
    }
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    html, body { font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, Arial, 'Helvetica Neue', sans-serif; }
    .required:after { content: ' *'; color: #ef4444; }
    .segmented input[type="radio"] { display: none; }
    .segmented label { flex: 1 1 0; text-align: center; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; cursor: pointer; font-weight: 600; }
    .segmented input[type="radio"]:checked + label { background-color: #0ea5e9; color: #fff; border-color: #0ea5e9; }
  </style>
</head>
<body class="bg-white">
  <div class="mx-auto max-w-md min-h-screen bg-white">
    <header class="sticky top-0 z-10 bg-white/95 backdrop-blur border-b border-slate-200">
      <div class="px-4 py-3">
        <h1 class="text-2xl font-bold text-slate-800">Book Spa</h1>
        <p class="text-sm text-slate-500">Easy version (mobile).</p>
      </div>
    </header>

    <main class="px-4 py-4">
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

      <?php if (!empty($validation)): ?>
      <div class="mb-4 rounded-md bg-yellow-50 p-4 ring-1 ring-yellow-200">
        <?= $validation; ?>
      </div>
      <?php endif; ?>

      <form method="post" action="<?= site_url('booking/submit'); ?>" novalidate class="space-y-5">

        <!-- Step 1: Package -->
        <div>
          <label class="required block text-sm font-semibold text-slate-700" for="package_id">Package</label>
          <select id="package_id" name="package_id" required class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-3 text-slate-900 shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary">
            <option value="">Select package</option>
            <?php if (!empty($packages)): ?>
              <?php foreach ($packages as $p): ?>
                <option value="<?= (int)$p->id; ?>">
                  <?php
                    $curr = isset($p->currency) ? $p->currency : 'Rp';
                    $pin  = isset($p->price_in_call) ? (float)$p->price_in_call : (isset($p->price) ? (float)$p->price : 0);
                    $pout = isset($p->price_out_call) ? (float)$p->price_out_call : null;
                  ?>
                  <?= htmlspecialchars($p->name); ?>
                  <?php if (isset($p->duration)): ?> - <?= (int)$p->duration; ?> minutes<?php endif; ?>
                  <?php if ($pin): ?> - IN: <?= htmlspecialchars($curr); ?> <?= number_format($pin, 0, ',', '.'); ?><?php endif; ?>
                  <?php if ($pout): ?> / OUT: <?= htmlspecialchars($curr); ?> <?= number_format($pout, 0, ',', '.'); ?><?php endif; ?>
                </option>
              <?php endforeach; ?>
            <?php endif; ?>
          </select>
        </div>

        <!-- Step 2: Therapist (optional) -->
        <div>
          <label class="block text-sm font-semibold text-slate-700" for="therapist_id">Therapist (optional)</label>
          <select id="therapist_id" name="therapist_id" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-3 text-slate-900 shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary">
            <option value="">Choose therapist (optional)</option>
            <?php if (!empty($therapists)): ?>
              <?php foreach ($therapists as $t): ?>
              <option value="<?= (int)$t->id; ?>">
                <?= htmlspecialchars($t->name); ?> <?= !empty($t->status) ? '(' . htmlspecialchars($t->status) . ')' : ''; ?>
              </option>
              <?php endforeach; ?>
            <?php endif; ?>
          </select>
          <p class="mt-1 text-xs text-slate-500">Time availability will adjust to selected therapist.</p>
        </div>

        <!-- Step 3: Call Type -->
        <div>
          <span class="required block text-sm font-semibold text-slate-700">Call Type</span>
          <div class="mt-2 grid grid-cols-2 gap-2 segmented">
            <input type="radio" id="call_in" name="call_type" value="IN" checked>
            <label for="call_in">In Call</label>
            <input type="radio" id="call_out" name="call_type" value="OUT">
            <label for="call_out">Out Call</label>
          </div>
          <p class="mt-1 text-xs text-slate-500">Price follows call type.</p>
        </div>

        <!-- Step 4: Date -->
        <div>
          <label class="required block text-sm font-semibold text-slate-700" for="date">Date</label>
          <input type="date" id="date" name="date" required min="<?= date('Y-m-d'); ?>" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-3 text-slate-900 shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary">
          <p class="mt-1 text-xs text-slate-500">Please select a date first.</p>
        </div>

        <!-- Step 5: Time (based on availability) -->
        <div>
          <span class="required block text-sm font-semibold text-slate-700">Select Time</span>
          <input type="hidden" id="time" name="time" required>
          <div id="slots" class="mt-2 grid grid-cols-3 sm:grid-cols-4 gap-2"></div>
          <div id="slots_help" class="mt-2 text-xs text-slate-500">Available times are in green.</div>
          <div id="slots_error" class="mt-2 hidden rounded bg-red-50 text-red-700 text-sm px-3 py-2"></div>
        </div>

        <!-- Step 6: Customer Data -->
        <div>
          <label class="required block text-sm font-semibold text-slate-700" for="customer_name">Name</label>
          <input type="text" id="customer_name" name="customer_name" required minlength="2" placeholder="Full name" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-3 text-slate-900 shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary">
        </div>
        <div>
          <label class="required block text-sm font-semibold text-slate-700" for="address">Address</label>
          <textarea id="address" name="address" rows="3" required minlength="5" placeholder="Full address" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-3 text-slate-900 shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
        </div>

        <div class="pt-2">
          <button type="submit" class="w-full rounded-xl bg-primary px-4 py-4 text-base font-semibold text-white shadow-sm transition hover:bg-sky-600 focus:outline-none focus:ring-2 focus:ring-primary disabled:opacity-50 disabled:cursor-not-allowed">Send Booking</button>
        </div>

      </form>
    </main>
  </div>

  <script>
  window.addEventListener('load', function () {
    const therapistSel = document.getElementById('therapist_id');
    const dateInput    = document.getElementById('date');
    const timeInput    = document.getElementById('time');
    const slotsWrap    = document.getElementById('slots');
    const slotsError   = document.getElementById('slots_error');
    const submitBtn    = document.querySelector('button[type="submit"]');
    const ENDPOINT     = '<?= site_url('booking/availability'); ?>';
    let selected = null;

    function renderLoading() {
      slotsWrap.innerHTML = '';
      for (let i = 0; i < 6; i++) {
        const s = document.createElement('div');
        s.className = 'h-12 rounded-lg bg-slate-200 animate-pulse';
        slotsWrap.appendChild(s);
      }
    }

    function setSubmitEnabled(enabled) {
      if (!submitBtn) return;
      submitBtn.disabled = !enabled;
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
          'h-12 rounded-lg text-sm font-semibold border px-3 ' +
          (isBooked
            ? 'bg-slate-100 text-slate-400 border-slate-200 cursor-not-allowed line-through'
            : 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100 focus:outline-none focus:ring-2 focus:ring-emerald-400');
        btn.disabled = isBooked;

        if (!isBooked) {
          btn.addEventListener('click', function () {
            selected = t;
            timeInput.value = t.length === 5 ? (t + ':00') : t;
            Array.prototype.forEach.call(slotsWrap.querySelectorAll('button'), function (b) {
              b.classList.remove('ring-2', 'ring-emerald-500', 'bg-emerald-200');
            });
            btn.classList.add('ring-2', 'ring-emerald-500', 'bg-emerald-200');
            setSubmitEnabled(true);
          });
        }
        slotsWrap.appendChild(btn);
      });
      if (!timeInput.value) setSubmitEnabled(false);
    }

    async function fetchSlots() {
      const date = dateInput ? dateInput.value : '';
      selected = null;
      timeInput.value = '';
      setSubmitEnabled(false);

      if (!date) {
        slotsWrap.innerHTML = '<div class="text-sm text-slate-500">Select a date to see available times.</div>';
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
        slotsError.textContent = 'Failed to load availability. Try again.';
        slotsError.classList.remove('hidden');
      }
    }

    if (dateInput) dateInput.addEventListener('change', fetchSlots);
    if (therapistSel) therapistSel.addEventListener('change', fetchSlots);

    // Initial message for users
    slotsWrap.innerHTML = '<div class="text-sm text-slate-500">Select a date to see available times.</div>';
    setSubmitEnabled(false);

    // If date is already filled (e.g. browser restore), load slots
    if (dateInput && dateInput.value) fetchSlots();
  });
  </script>
</body>
</html>