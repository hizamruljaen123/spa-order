<?php
$this->load->view('admin/layout/header', ['title' => isset($title) ? $title : 'Kalendar Jadual']);
?>
<!-- Page: Schedule Calendar -->
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
  <div>
    <h2 class="text-base font-semibold text-gray-900">Kalendar Jadual</h2>
    <p class="text-sm text-gray-600">Pantau dan urus jadual tempahan menggunakan kalendar interaktif.</p>
  </div>
  <div class="flex items-center gap-3">
    <label for="therapistFilter" class="text-sm text-gray-700">Tapis Terapis</label>
    <select id="therapistFilter" class="rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm">
      <option value="">Semua Terapis</option>
      <?php if (!empty($therapists)): ?>
        <?php foreach ($therapists as $t): ?>
          <option value="<?= (int)$t->id; ?>"><?= htmlspecialchars($t->name); ?></option>
        <?php endforeach; ?>
      <?php endif; ?>
    </select>
  </div>
</div>

<!-- Ringkasan Bulanan -->
<div id="monthSummary" class="mb-3 rounded-lg border border-gray-200 bg-white px-4 py-3 shadow-sm">
  <div class="flex items-center justify-between">
    <div class="text-sm text-gray-500">Jumlah pesanan pada bulan ini</div>
    <div class="text-xl md:text-3xl font-extrabold tracking-tight"><span id="monthTotal">0</span> Orders</div>
  </div>
</div>

<!-- Calendar container -->
<div id="calendar" class="rounded-lg border border-gray-200 bg-white p-3 shadow-sm"></div>

<!-- Legend: Kepadatan Jadual -->
<div class="mt-3 rounded-lg border border-gray-200 bg-white p-3 shadow-sm">
  <div class="flex flex-wrap items-center gap-3 text-xs">
    <span class="font-medium text-gray-700">Legenda Kepadatan Jadual:</span>
    <span class="inline-flex items-center gap-2">
      <span class="inline-flex items-center gap-1">
        <span class="h-3 w-3 rounded bg-green-300 border border-green-500"></span><span>1–2 Orders (Rendah)</span>
      </span>
      <span class="inline-flex items-center gap-1">
        <span class="h-3 w-3 rounded bg-yellow-300 border border-yellow-500"></span><span>3–5 Orders (Sederhana)</span>
      </span>
      <span class="inline-flex items-center gap-1">
        <span class="h-3 w-3 rounded bg-orange-300 border border-orange-500"></span><span>6–8 Orders (Tinggi)</span>
      </span>
      <span class="inline-flex items-center gap-1">
        <span class="h-3 w-3 rounded bg-red-400 border border-red-600"></span><span>9+ Orders (Sangat Padat)</span>
      </span>
    </span>
  </div>
  <p class="mt-2 text-[11px] text-gray-500">Semakin padat jadual, warna akan berubah dari hijau → kuning → jingga → merah.</p>
</div>

<!-- Tailwind modal (details) -->
<div id="bookingModal" class="fixed inset-0 z-40 hidden" aria-hidden="true">
  <div class="absolute inset-0 bg-black/40"></div>
  <div class="absolute inset-0 flex items-center justify-center p-4">
    <div class="w-full max-w-md rounded-lg bg-white shadow-lg">
      <div class="px-5 py-3 border-b border-gray-200 flex items-center justify-between">
        <h3 class="text-sm font-medium text-gray-900">Butiran Tempahan</h3>
        <button id="closeModalBtn" class="inline-flex items-center rounded-md border border-gray-300 bg-white text-gray-700 px-2 py-1 text-xs hover:bg-gray-50">Tutup</button>
      </div>
      <div class="p-5">
        <dl class="text-sm text-gray-800 space-y-2">
          <div class="flex">
            <dt class="w-32 text-gray-600">Pelanggan</dt>
            <dd id="bd_customer" class="flex-1">-</dd>
          </div>
          <div class="flex">
            <dt class="w-32 text-gray-600">Pakej</dt>
            <dd id="bd_package" class="flex-1">-</dd>
          </div>
          <div class="flex">
            <dt class="w-32 text-gray-600">Terapis</dt>
            <dd id="bd_therapist" class="flex-1">-</dd>
          </div>
          <div class="flex">
            <dt class="w-32 text-gray-600">Tarikh</dt>
            <dd id="bd_date" class="flex-1">-</dd>
          </div>
          <div class="flex">
            <dt class="w-32 text-gray-600">Masa</dt>
            <dd id="bd_time" class="flex-1">-</dd>
          </div>
          <div class="flex">
            <dt class="w-32 text-gray-600">Status</dt>
            <dd class="flex-1"><span id="bd_status" class="inline-flex items-center px-2 py-1 rounded bg-gray-200 text-gray-700 text-xs">-</span></dd>
          </div>
        </dl>
      </div>

      <!-- Seksi Edit Waktu (disembunyikan secara default) -->
      <div id="editSection" class="px-5 py-3 border-t border-gray-100 hidden">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div>
            <label for="edit_date" class="block text-xs font-medium text-gray-700">Tarikh</label>
            <input type="date" id="edit_date" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm">
          </div>
          <div>
            <label for="edit_time" class="block text-xs font-medium text-gray-700">Masa</label>
            <input type="time" id="edit_time" step="60" class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm">
          </div>
        </div>
        <div class="mt-3 flex items-center justify-end gap-2">
          <button id="editCancelBtn" class="inline-flex items-center rounded-md border border-gray-300 bg-white text-gray-700 px-3 py-1.5 text-xs hover:bg-gray-50">Batal</button>
          <button id="editSaveBtn" class="inline-flex items-center rounded-md bg-sky-600 text-white px-3 py-1.5 text-xs hover:bg-sky-700">Simpan</button>
        </div>
      </div>

      <div class="px-5 py-3 border-t border-gray-200 flex items-center justify-end gap-2">
        <button id="editBtn" class="inline-flex items-center rounded-md border border-gray-300 bg-white text-gray-700 px-3 py-1.5 text-xs hover:bg-gray-50">Edit Waktu</button>
        <button id="deleteBtn" class="inline-flex items-center rounded-md bg-red-600 text-white px-3 py-1.5 text-xs hover:bg-red-700">Hapus</button>
        <a href="#" id="bd_invoice_btn" target="_blank" rel="noopener" class="inline-flex items-center rounded-md border border-gray-300 bg-white text-gray-700 px-3 py-1.5 text-xs hover:bg-gray-50">Lihat Invois</a>
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
  // CSRF disabled; tokens not used
  const therapistFilter = document.getElementById('therapistFilter');
  const modalEl = document.getElementById('bookingModal');
  const closeModalBtn = document.getElementById('closeModalBtn');
  const closeModalBtn2 = document.getElementById('closeModalBtn2');
  const editSection = document.getElementById('editSection');
  const editBtn = document.getElementById('editBtn');
  const deleteBtn = document.getElementById('deleteBtn');
  const editDateInput = document.getElementById('edit_date');
  const editTimeInput = document.getElementById('edit_time');
  const editSaveBtn = document.getElementById('editSaveBtn');
  const editCancelBtn = document.getElementById('editCancelBtn');
  const invoiceBtn = document.getElementById('bd_invoice_btn');

  // Current event yang sedang dibuka di modal
  let currentEvent = null;

  // CSRF (jika diaktifkan)
  const CI_CSRF = {
    name: '',
    hash: ''
  };

  // Peta jumlah tempahan per hari (YYYY-MM-DD -> count)
  let countsByDate = {};

  // Helper: format tarikh tempatan ke YYYY-MM-DD
  function toYMD(dt) {
    if (!dt) return '';
    const y = dt.getFullYear();
    const m = String(dt.getMonth() + 1).padStart(2, '0');
    const d = String(dt.getDate()).padStart(2, '0');
    return `${y}-${m}-${d}`;
  }

  // Helper: format masa tempatan ke HH:MM
  function toHM(dt) {
    if (!dt) return '';
    const h = String(dt.getHours()).padStart(2, '0');
    const m = String(dt.getMinutes()).padStart(2, '0');
    return `${h}:${m}`;
  }

  // Helper: POST x-www-form-urlencoded dengan optional CSRF
  function apiPost(url, params) {
    const body = new URLSearchParams(params || {});
    if (CI_CSRF.name && CI_CSRF.hash) {
      body.append(CI_CSRF.name, CI_CSRF.hash);
    }
    return fetch(url, {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      credentials: 'same-origin',
      body
    }).then(resp => {
      if (!resp.ok) throw new Error('HTTP ' + resp.status);
      return resp.json().catch(() => ({}));
    });
  }

  function toggleEditSection(show) {
    if (!editSection) return;
    if (show) {
      editSection.classList.remove('hidden');
    } else {
      editSection.classList.add('hidden');
    }
  }

  // Aksi tombol modal
  if (editBtn) {
    editBtn.addEventListener('click', function() {
      if (!currentEvent) return;
      // Prefill dari event yang dipilih
      editDateInput.value = toYMD(currentEvent.start) || '';
      editTimeInput.value = toHM(currentEvent.start) || '';
      toggleEditSection(true);
    });
  }
  if (editCancelBtn) {
    editCancelBtn.addEventListener('click', function() {
      toggleEditSection(false);
    });
  }
  if (editSaveBtn) {
    editSaveBtn.addEventListener('click', function() {
      if (!currentEvent) return;
      const d = (editDateInput.value || '').trim();
      const t = (editTimeInput.value || '').trim();
      if (!d || !t) {
        alert('Tarikh dan masa wajib diisi.');
        return;
      }
      apiPost('<?= site_url('admin/booking/update-time'); ?>', {
        token: currentEvent.id,
        date: d,
        time: t
      }).then(res => {
        if (!res || !res.ok) throw new Error(res && res.error ? res.error : 'Gagal menyimpan perubahan.');
        // Update posisi event secara lokal
        const newStart = new Date(d + 'T' + t + ':00');
        currentEvent.setStart(newStart);
        toggleEditSection(false);
        closeModal();
      }).catch(err => {
        alert('Gagal mengubah waktu: ' + err.message);
      }).finally(() => {
        // Segarkan data agar konsisten
        if (typeof calendar !== 'undefined') {
          calendar.refetchEvents();
        }
      });
    });
  }
  if (deleteBtn) {
    deleteBtn.addEventListener('click', function() {
      if (!currentEvent) return;
      if (!confirm('Yakin ingin menghapus booking ini?')) return;
      apiPost('<?= site_url('admin/booking/delete'); ?>', { token: currentEvent.id })
        .then(res => {
          if (!res || !res.ok) throw new Error(res && res.error ? res.error : 'Gagal menghapus.');
          // Hapus dari kalendar secara lokal
          currentEvent.remove();
          closeModal();
        })
        .catch(err => {
          alert('Gagal menghapus: ' + err.message);
        })
        .finally(() => {
          if (typeof calendar !== 'undefined') {
            calendar.refetchEvents();
          }
        });
    });
  }

  function computeCounts(events) {
    const map = {};
    if (Array.isArray(events)) {
      events.forEach(ev => {
        const s = ev.start || ev.startStr || '';
        const str = String(s);
        const date = str.slice(0, 10); // 'YYYY-MM-DD'
        if (/^\d{4}-\d{2}-\d{2}$/.test(date)) {
          map[date] = (map[date] || 0) + 1;
        }
      });
    }
    return map;
  }

  // Mapping warna berdasarkan jumlah (heatmap)
  function getHeatClasses(count) {
    if (count >= 9) return { bg: 'bg-red-200', text: 'text-red-900' };
    if (count >= 6) return { bg: 'bg-orange-200', text: 'text-orange-900' };
    if (count >= 3) return { bg: 'bg-yellow-200', text: 'text-yellow-900' };
    if (count >= 1) return { bg: 'bg-green-200', text: 'text-green-900' };
    return { bg: 'bg-gray-50', text: 'text-gray-400' };
  }

  // Render heatmap + tulisan besar "X Orders" di setiap sel hari (month view sahaja)
  function renderDensityCells() {
    if (!calendar) return;
    const isMonth = calendar.view.type === 'dayGridMonth';
    const cells = calendarEl.querySelectorAll('.fc-daygrid-day');
    cells.forEach(cell => {
      const frame = cell.querySelector('.fc-daygrid-day-frame');
      const topEl = cell.querySelector('.fc-daygrid-day-top');
      if (!frame || !topEl) return;

      frame.classList.add('relative');
      topEl.classList.add('relative', 'z-10');
      topEl.classList.add('relative', 'z-10');

      // Bersihkan elemen overlay sebelumnya
      const oldOverlay = frame.querySelector('.order-density-overlay');
      if (oldOverlay) oldOverlay.remove();
      const oldLabel = frame.querySelector('.order-density-label');
      if (oldLabel) oldLabel.remove();

      if (!isMonth) return;

      const date = cell.getAttribute('data-date');
      const count = (date && countsByDate[date]) ? countsByDate[date] : 0;
      const { bg, text } = getHeatClasses(count);

      // Overlay warna (heatmap) — berada di bawah konten tanggal
      const overlay = document.createElement('div');
      overlay.className = 'order-density-overlay pointer-events-none absolute inset-0 z-0 rounded-sm ' + bg + ' opacity-60';
      frame.appendChild(overlay);

      // Tulisan besar "X Orders" — berada di atas overlay
      const label = document.createElement('div');
      label.className = 'order-density-label pointer-events-none absolute inset-0 z-20 flex items-center justify-center text-center font-extrabold ' + text + ' select-none text-base md:text-2xl';
      label.textContent = count > 0 ? (count + ' Orders') : '';
      frame.appendChild(label);
    });
  }

  function updateMonthSummary() {
    if (!calendar) return;
    const start = calendar.view.activeStart;
    const end = calendar.view.activeEnd;
    let total = 0;
    for (const d in countsByDate) {
      if (Object.prototype.hasOwnProperty.call(countsByDate, d)) {
        const dt = new Date(d + 'T00:00:00');
        if (dt >= start && dt < end) {
          total += countsByDate[d];
        }
      }
    }
    const el = document.getElementById('monthTotal');
    if (el) el.textContent = total;
  }

  function openModal() {
    modalEl.classList.remove('hidden');
    modalEl.setAttribute('aria-hidden', 'false');
  }
  function closeModal() {
    modalEl.classList.add('hidden');
    modalEl.setAttribute('aria-hidden', 'true');
    // Reset state edit + event
    if (typeof editSection !== 'undefined' && editSection) {
      editSection.classList.add('hidden');
    }
    currentEvent = null;
  }

  [closeModalBtn, closeModalBtn2].forEach(btn => btn.addEventListener('click', closeModal));
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeModal();
  });

  const calendarEl = document.getElementById('calendar');
  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    locale: 'ms',
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
    },
    height: 'auto',
    nowIndicator: true,
    eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
    editable: false,               // akan diaktifkan hanya pada paparan harian
    eventDurationEditable: false,  // tidak izinkan resize (DB hanya simpan start)
    // Sembunyikan event di paparan bulan, hanya tunjuk jumlah order per hari
    views: {
      dayGridMonth: {
        eventDisplay: 'none'
      }
    },
    navLinks: true,
    dateClick: function(info) {
      // Tukar ke paparan hari apabila tarikh diklik dan tampilkan jadwal (events)
      if (calendar) {
        calendar.changeView('timeGridDay', info.dateStr);
        calendar.refetchEvents();
      }
    },
    // Kemas kini heatmap + tulisan jumlah bila tukar bulan / view
    datesSet: function() {
      // Toggle drag-n-drop hanya pada paparan harian
      const isDay = calendar.view.type === 'timeGridDay';
      calendar.setOption('editable', isDay);
      setTimeout(renderDensityCells, 0);
    },

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
      .then(data => {
        // Kira jumlah tempahan per hari dan simpan
        countsByDate = computeCounts(data);
        // Pada paparan bulan, jangan tampilkan event pada cell tanggal
        if (calendar.view.type === 'dayGridMonth') {
          successCallback([]); // kosongkan event di grid bulan
          setTimeout(renderDensityCells, 0);
        } else {
          // Selain paparan bulan (mingguan/harian), tampilkan event seperti biasa
          successCallback(data);
        }
        // Kemas kini ringkasan jumlah bulanan
        updateMonthSummary();
      })
      .catch(err => failureCallback(err));
    },

    // Drag & drop untuk ubah waktu (harian sahaja)
    eventDrop: function(info) {
      const ev = info.event;
      const newDate = toYMD(ev.start);
      const newTime = toHM(ev.start) + ':00';
      apiPost('<?= site_url('admin/booking/update-time'); ?>', {
        token: ev.id,
        date: newDate,
        time: newTime
      }).then(res => {
        if (!res || !res.ok) throw new Error(res && res.error ? res.error : 'Gagal menyimpan perubahan.');
      }).catch(err => {
        alert('Gagal mengubah jadwal: ' + err.message);
        info.revert();
      }).finally(() => {
        // Segarkan untuk konsistensi ringkasan/overlay bila perlu
        calendar.refetchEvents();
      });
    },

    eventClick: function(info) {
      const ev = info.event;
      const props = ev.extendedProps || {};
      const start = ev.start;

      // Simpan event aktif
      currentEvent = ev;

      document.getElementById('bd_customer').textContent = props.customer_name || '-';
      document.getElementById('bd_package').textContent = props.package_name || '-';
      document.getElementById('bd_therapist').textContent = props.therapist_name || '-';
      document.getElementById('bd_status').textContent = (props.status || '-');

      const dateStr = start ? start.toLocaleDateString('id-ID') : '-';
      const timeStr = start ? start.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', hour12: false }) : '-';
      document.getElementById('bd_date').textContent = dateStr;
      document.getElementById('bd_time').textContent = timeStr;

      if (invoiceBtn) {
        if (ev.id) {
          invoiceBtn.href = '<?= site_url('admin/invoice'); ?>/' + ev.id;
          invoiceBtn.classList.remove('opacity-50', 'pointer-events-none');
        } else {
          invoiceBtn.href = '#';
          invoiceBtn.classList.add('opacity-50', 'pointer-events-none');
        }
      }

      // Prefill form edit (disembunyikan dulu)
      if (editDateInput && editTimeInput) {
        editDateInput.value = toYMD(start) || '';
        editTimeInput.value = toHM(start) || '';
      }
      toggleEditSection(false);

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