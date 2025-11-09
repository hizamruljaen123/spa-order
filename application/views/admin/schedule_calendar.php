<?php
$this->load->view('admin/layout/header', ['title' => isset($title) ? $title : 'Kalender Jadwal']);
?>
<!-- Page: Schedule Calendar -->
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
  <div>
    <h2 class="text-base font-semibold text-gray-900">Kalender Jadwal</h2>
    <p class="text-sm text-gray-600">Pantau dan kelola jadwal booking menggunakan kalender interaktif.</p>
  </div>
  <div class="flex items-center gap-3">
    <label for="therapistFilter" class="text-sm text-gray-700">Filter Therapist</label>
    <select id="therapistFilter" class="rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm">
      <option value="">Semua Therapist</option>
      <?php if (!empty($therapists)): ?>
        <?php foreach ($therapists as $t): ?>
          <option value="<?= (int)$t->id; ?>"><?= htmlspecialchars($t->name); ?></option>
        <?php endforeach; ?>
      <?php endif; ?>
    </select>
  </div>
</div>

<!-- Calendar container -->
<div id="calendar" class="rounded-lg border border-gray-200 bg-white p-3 shadow-sm"></div>

<!-- Tailwind modal (details) -->
<div id="bookingModal" class="fixed inset-0 z-40 hidden" aria-hidden="true">
  <div class="absolute inset-0 bg-black/40"></div>
  <div class="absolute inset-0 flex items-center justify-center p-4">
    <div class="w-full max-w-md rounded-lg bg-white shadow-lg">
      <div class="px-5 py-3 border-b border-gray-200 flex items-center justify-between">
        <h3 class="text-sm font-medium text-gray-900">Detail Booking</h3>
        <button id="closeModalBtn" class="inline-flex items-center rounded-md border border-gray-300 bg-white text-gray-700 px-2 py-1 text-xs hover:bg-gray-50">Tutup</button>
      </div>
      <div class="p-5">
        <dl class="text-sm text-gray-800 space-y-2">
          <div class="flex">
            <dt class="w-32 text-gray-600">Pelanggan</dt>
            <dd id="bd_customer" class="flex-1">-</dd>
          </div>
          <div class="flex">
            <dt class="w-32 text-gray-600">Paket</dt>
            <dd id="bd_package" class="flex-1">-</dd>
          </div>
          <div class="flex">
            <dt class="w-32 text-gray-600">Therapist</dt>
            <dd id="bd_therapist" class="flex-1">-</dd>
          </div>
          <div class="flex">
            <dt class="w-32 text-gray-600">Tanggal</dt>
            <dd id="bd_date" class="flex-1">-</dd>
          </div>
          <div class="flex">
            <dt class="w-32 text-gray-600">Jam</dt>
            <dd id="bd_time" class="flex-1">-</dd>
          </div>
          <div class="flex">
            <dt class="w-32 text-gray-600">Status</dt>
            <dd class="flex-1"><span id="bd_status" class="inline-flex items-center px-2 py-1 rounded bg-gray-200 text-gray-700 text-xs">-</span></dd>
          </div>
        </dl>
      </div>
      <div class="px-5 py-3 border-t border-gray-200 flex items-center justify-end gap-2">
        <a href="#" id="bd_invoice_btn" target="_blank" rel="noopener" class="inline-flex items-center rounded-md border border-gray-300 bg-white text-gray-700 px-3 py-1.5 text-xs hover:bg-gray-50">Lihat Invoice</a>
        <button id="closeModalBtn2" class="inline-flex items-center rounded-md bg-sky-600 text-white px-3 py-1.5 text-xs hover:bg-sky-700">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- FullCalendar CSS (allowed in body for simplicity in modular layout) -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css" rel="stylesheet">

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/locales-all.min.js"></script>
<script>
(function() {
  const therapistFilter = document.getElementById('therapistFilter');
  const modalEl = document.getElementById('bookingModal');
  const closeModalBtn = document.getElementById('closeModalBtn');
  const closeModalBtn2 = document.getElementById('closeModalBtn2');

  function openModal() {
    modalEl.classList.remove('hidden');
    modalEl.setAttribute('aria-hidden', 'false');
  }
  function closeModal() {
    modalEl.classList.add('hidden');
    modalEl.setAttribute('aria-hidden', 'true');
  }

  [closeModalBtn, closeModalBtn2].forEach(btn => btn.addEventListener('click', closeModal));
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeModal();
  });

  const calendarEl = document.getElementById('calendar');
  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    locale: 'id',
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
    },
    height: 'auto',
    eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },

    events: function(fetchInfo, successCallback, failureCallback) {
      const params = new URLSearchParams();
      params.set('start', fetchInfo.startStr);
      params.set('end', fetchInfo.endStr);
      const therapistId = therapistFilter.value;
      if (therapistId) params.set('therapist_id', therapistId);

      fetch('<?= site_url('admin/schedule'); ?>' + '?' + params.toString(), {
        method: 'GET',
        headers: { 'Accept': 'application/json' },
        credentials: 'same-origin'
      })
      .then(resp => {
        if (!resp.ok) throw new Error('HTTP ' + resp.status);
        return resp.json();
      })
      .then(data => successCallback(data))
      .catch(err => failureCallback(err));
    },

    eventClick: function(info) {
      const ev = info.event;
      const props = ev.extendedProps || {};
      const start = ev.start;

      document.getElementById('bd_customer').textContent = props.customer_name || '-';
      document.getElementById('bd_package').textContent = props.package_name || '-';
      document.getElementById('bd_therapist').textContent = props.therapist_name || '-';
      document.getElementById('bd_status').textContent = (props.status || '-');

      const dateStr = start ? start.toLocaleDateString('id-ID') : '-';
      const timeStr = start ? start.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', hour12: false }) : '-';
      document.getElementById('bd_date').textContent = dateStr;
      document.getElementById('bd_time').textContent = timeStr;

      const invoiceBtn = document.getElementById('bd_invoice_btn');
      if (ev.id) {
        invoiceBtn.href = '<?= site_url('admin/invoice'); ?>/' + ev.id;
        invoiceBtn.classList.remove('opacity-50', 'pointer-events-none');
      } else {
        invoiceBtn.href = '#';
        invoiceBtn.classList.add('opacity-50', 'pointer-events-none');
      }

      openModal();
    }
  });

  calendar.render();

  therapistFilter.addEventListener('change', function() {
    calendar.refetchEvents();
  });
})();
</script>

<?php $this->load->view('admin/layout/footer'); ?>